<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $articles = \App\Models\Article::where('is_published', true)->latest()->take(3)->get();
    $teams = \App\Models\Team::all();
    
    // Dynamic Settings
    $settings = \App\Models\Setting::whereIn('key', ['landing_title', 'landing_subtitle'])->pluck('value', 'key')->toArray();
    $landingTitle = $settings['landing_title'] ?? 'AFFA Swimming Academy';
    $landingSubtitle = $settings['landing_subtitle'] ?? 'We provide professional swimming lessons for all ages with experienced and certified instructors.';
    
    // Dynamic Stats
    $totalCoaches = \App\Models\User::where('role', 'pelatih')->count();
    $activeStudents = \App\Models\Student::where('remaining_meetings', '>', 0)
                        ->whereDate('package_active_until', '>=', now())
                        ->count();
    $totalAlumni = \App\Models\Student::count();

    return view('welcome', compact('articles', 'teams', 'landingTitle', 'landingSubtitle', 'totalCoaches', 'activeStudents', 'totalAlumni'));
});

Route::get('/tentang-kami', function () {
    $teams = \App\Models\Team::all();
    $settings = \App\Models\Setting::whereIn('key', ['about_owner_message', 'about_owner_photo'])->pluck('value', 'key')->toArray();
    $aboutOwnerMessage = $settings['about_owner_message'] ?? "Kami berdedikasi untuk memberikan pelatihan renang terbaik untuk seluruh generasi.";
    $aboutOwnerPhoto = $settings['about_owner_photo'] ?? null;
    return view('about', compact('teams', 'aboutOwnerMessage', 'aboutOwnerPhoto'));
});

Route::get('/kontak', function () {
    $keys = ['contact_address', 'contact_phone', 'contact_email', 'contact_instagram', 'contact_map_embed'];
    $settings = \App\Models\Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
    
    $contactAddress = $settings['contact_address'] ?? null;
    $contactPhone = $settings['contact_phone'] ?? null;
    $contactEmail = $settings['contact_email'] ?? null;
    $contactInstagram = $settings['contact_instagram'] ?? null;
    $contactMapEmbed = $settings['contact_map_embed'] ?? null;

    return view('contact', compact('contactAddress', 'contactPhone', 'contactEmail', 'contactInstagram', 'contactMapEmbed'));
});

// Core Dashboard Redirector
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    switch ($role) {
        case 'master':
            return redirect()->route('master.dashboard');
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'pelatih':
            return redirect()->route('pelatih.dashboard');
        case 'murid':
        default:
            return redirect()->route('murid.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// Master Dashboard Group
Route::middleware(['auth', 'role:master'])->group(function () {
    Route::get('/master/dashboard', function () {
        $now = \Carbon\Carbon::now();
        $startOfMonth = $now->startOfMonth()->format('Y-m-d H:i:s');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d H:i:s');

        // Total Pendapatan Kas (bersih) bulan ini
        $totalCash = \App\Models\Transaction::where('status', 'approved')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->sum('cash_cut');

        // Total Pengeluaran Operasional bulan ini
        $operationalExpenses = \App\Models\OperationalExpense::whereBetween('expense_date', [
            $now->startOfMonth()->format('Y-m-d'), 
            $now->copy()->endOfMonth()->format('Y-m-d')
        ])->sum('amount');

        // Total Gaji Pelatih bulan ini (berdasarkan reports)
        // Gabungkan training reports yang hadir di bulan ini dan hitung coach_fee dari relasi poolLocation
        $trainingReports = \App\Models\TrainingReport::with('schedule.poolLocation')
            ->where('coach_attendance', 'Hadir')
            ->whereBetween('training_date', [
                $now->startOfMonth()->format('Y-m-d'), 
                $now->copy()->endOfMonth()->format('Y-m-d')
            ])->get();

        $coachSalaryExpenses = $trainingReports->sum(function ($report) {
            return $report->schedule->poolLocation->coach_fee ?? 0;
        });

        $totalExpenses = $operationalExpenses + $coachSalaryExpenses;

        // Jumlah Murid Aktif
        $activeStudents = \App\Models\Student::where('remaining_meetings', '>', 0)
            ->whereDate('package_active_until', '>=', \Carbon\Carbon::today())
            ->count();

        // Distribusi Paket per Kolam
        $packageDistribution = \Illuminate\Support\Facades\DB::table('transactions')
            ->join('pool_locations', 'transactions.pool_location_id', '=', 'pool_locations.id')
            ->select('pool_locations.name as pool_name', 'pool_locations.meeting_count as package_type', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->where('transactions.status', 'approved')
            ->groupBy('pool_locations.name', 'pool_locations.meeting_count')
            ->get();

        return view('master.dashboard', compact('totalCash', 'totalExpenses', 'activeStudents', 'packageDistribution', 'operationalExpenses', 'coachSalaryExpenses'));
    })->name('master.dashboard');


    Route::patch('positions/{position}/toggle-status', [\App\Http\Controllers\Master\PositionController::class, 'toggleStatus'])->name('master.positions.toggle-status');
    Route::resource('positions', \App\Http\Controllers\Master\PositionController::class)->names([
        'index' => 'master.positions.index',
        'create' => 'master.positions.create',
        'store' => 'master.positions.store',
        'edit' => 'master.positions.edit',
        'update' => 'master.positions.update',
        'destroy' => 'master.positions.destroy',
    ]);

    // Moved to shared group below: articles, teams, students, swim-classes

    // Master Settings
    Route::get('/master/settings/landing', [\App\Http\Controllers\Master\LandingPageController::class, 'edit'])->name('master.settings.landing');
    Route::put('/master/settings/landing', [\App\Http\Controllers\Master\LandingPageController::class, 'update'])->name('master.settings.landing.update');
    Route::get('/master/settings/pages', [\App\Http\Controllers\Master\PageSettingController::class, 'edit'])->name('master.settings.pages');
    Route::put('/master/settings/pages', [\App\Http\Controllers\Master\PageSettingController::class, 'update'])->name('master.settings.pages.update');

    // Schedule Deletions
    Route::get('/master/schedule-deletions', [\App\Http\Controllers\Master\ScheduleDeletionController::class, 'index'])->name('master.schedule-deletions.index');
    Route::post('/master/schedule-deletions/{scheduleRequest}/approve', [\App\Http\Controllers\Master\ScheduleDeletionController::class, 'approve'])->name('master.schedule-deletions.approve');
    Route::post('/master/schedule-deletions/{scheduleRequest}/reject', [\App\Http\Controllers\Master\ScheduleDeletionController::class, 'reject'])->name('master.schedule-deletions.reject');
});

// Shared Master & Admin Group
Route::middleware(['auth', 'role:master,admin'])->group(function () {
    Route::get('/master/users', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\User::where(function($q) {
            $q->where('status', '!=', 'pending')->orWhereNull('status');
        });

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();
        return view('master.users', compact('users'));
    })->name('master.users');

    Route::get('/master/users/create', function () {
        $positions = \App\Models\Position::where('status', 'aktif')->get();
        return view('master.users-create', compact('positions'));
    })->name('master.users.create');

    Route::post('/master/users', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.\App\Models\User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,pelatih,murid'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ]);

        \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
            'position_id' => $validated['position_id'] ?? null,
            'status' => 'approved',
        ]);

        return redirect()->route('master.users')->with('success', 'Pengguna baru berhasil ditambahkan!');
    })->name('master.users.store');

    Route::get('/master/users/{user}/edit', function (\App\Models\User $user) {
        $positions = \App\Models\Position::where('status', 'aktif')->get();
        return view('master.users-edit', compact('user', 'positions'));
    })->name('master.users.edit');

    Route::put('/master/users/{user}', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:master,admin,pelatih,murid'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'position_id' => $validated['position_id'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('master.users')->with('success', 'Data pengguna berhasil diperbarui!');
    })->name('master.users.update');

    Route::delete('/master/users/{user}', function (\App\Models\User $user) {
        if (\Illuminate\Support\Facades\Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }
        $user->delete();
        return redirect()->route('master.users')->with('success', 'Pengguna berhasil dihapus!');
    })->name('master.users.destroy');

    Route::resource('articles', \App\Http\Controllers\Master\ArticleController::class)->names([
        'index' => 'master.articles.index',
        'create' => 'master.articles.create',
        'store' => 'master.articles.store',
        'edit' => 'master.articles.edit',
        'update' => 'master.articles.update',
        'destroy' => 'master.articles.destroy',
    ]);

    Route::resource('teams', \App\Http\Controllers\Master\TeamController::class)->names([
        'index' => 'master.teams.index',
        'create' => 'master.teams.create',
        'store' => 'master.teams.store',
        'edit' => 'master.teams.edit',
        'update' => 'master.teams.update',
        'destroy' => 'master.teams.destroy',
    ]);

    Route::patch('students/{student}/toggle-status', [\App\Http\Controllers\Master\StudentController::class, 'toggleStatus'])->name('master.students.toggle-status');
    Route::post('students/import', [\App\Http\Controllers\Master\StudentController::class, 'importData'])->name('master.students.import');
    Route::get('students/import/template', [\App\Http\Controllers\Master\StudentController::class, 'downloadTemplate'])->name('master.students.import.template');
    Route::get('students/export', [\App\Http\Controllers\Master\StudentController::class, 'exportData'])->name('master.students.export');
    Route::resource('students', \App\Http\Controllers\Master\StudentController::class)->names([
        'index' => 'master.students.index',
        'create' => 'master.students.create',
        'store' => 'master.students.store',
        'edit' => 'master.students.edit',
        'update' => 'master.students.update',
        'destroy' => 'master.students.destroy',
    ]);

    Route::patch('swim-classes/{swim_class}/toggle-status', [\App\Http\Controllers\Master\SwimClassController::class, 'toggleStatus'])->name('master.swim-classes.toggle-status');
    Route::resource('swim-classes', \App\Http\Controllers\Master\SwimClassController::class)->names([
        'index' => 'master.swim-classes.index',
        'create' => 'master.swim-classes.create',
        'store' => 'master.swim-classes.store',
        'edit' => 'master.swim-classes.edit',
        'update' => 'master.swim-classes.update',
        'destroy' => 'master.swim-classes.destroy',
    ]);

    // Shared Operations
    Route::get('operations/recap/manual/create', [\App\Http\Controllers\Admin\OperationalController::class, 'createManualRecap'])->name('admin.operations.createManualRecap');
    Route::post('operations/recap/manual', [\App\Http\Controllers\Admin\OperationalController::class, 'storeManualRecap'])->name('admin.operations.storeManualRecap');
    
    Route::get('/operations/recap', [\App\Http\Controllers\Admin\OperationalController::class, 'dailyRecap'])->name('admin.operations.recap');
    Route::get('/operations/recap/{trainingReport}', [\App\Http\Controllers\Admin\OperationalController::class, 'showRecap'])->name('admin.operations.showRecap');
    Route::delete('/operations/recap/{trainingReport}', [\App\Http\Controllers\Admin\OperationalController::class, 'destroyRecap'])->name('admin.operations.destroyRecap');
    // Shared Finance Operations (Master & Admin)
    Route::get('/finance/payments', [\App\Http\Controllers\FinanceController::class, 'payments'])->name('finance.payments.index');
    Route::get('/finance/payments/create', [\App\Http\Controllers\FinanceController::class, 'create'])->name('finance.payments.create');
    Route::post('/finance/payments', [\App\Http\Controllers\FinanceController::class, 'store'])->name('finance.payments.store');
    Route::get('/finance/payments/{transaction}/edit', [\App\Http\Controllers\FinanceController::class, 'edit'])->name('finance.payments.edit');
    Route::put('/finance/payments/{transaction}', [\App\Http\Controllers\FinanceController::class, 'update'])->name('finance.payments.update');
    Route::post('/finance/payments/{transaction}/approve', [\App\Http\Controllers\FinanceController::class, 'approve'])->name('finance.payments.approve');
    Route::post('/finance/payments/{transaction}/reject', [\App\Http\Controllers\FinanceController::class, 'reject'])->name('finance.payments.reject');
    Route::post('/finance/payments/{transaction}/settle', [\App\Http\Controllers\FinanceController::class, 'settle'])->name('finance.payments.settle');
    Route::delete('/finance/payments/{transaction}', [\App\Http\Controllers\FinanceController::class, 'destroy'])->name('finance.payments.destroy');
});

// Shared Master & Admin Group
Route::middleware(['auth', 'role:master,admin'])->group(function () {
    Route::resource('/admin/pool-locations', \App\Http\Controllers\PoolLocationController::class)->names([
        'index' => 'admin.pool-locations.index',
        'create' => 'admin.pool-locations.create',
        'store' => 'admin.pool-locations.store',
        'edit' => 'admin.pool-locations.edit',
        'update' => 'admin.pool-locations.update',
        'destroy' => 'admin.pool-locations.destroy',
    ]);

    // Shared Finance Operations
    Route::get('/finance/students/expired', [\App\Http\Controllers\FinanceController::class, 'expiredStudents'])->name('finance.students.expired');
    Route::get('/finance/expenses', [\App\Http\Controllers\FinanceController::class, 'expenses'])->name('finance.expenses.index');
    Route::post('/finance/expenses', [\App\Http\Controllers\FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
    Route::delete('/finance/expenses/{expense}', [\App\Http\Controllers\FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');
    
    // Unpaid Students
    Route::get('/finance/unpaid', [\App\Http\Controllers\FinanceController::class, 'unpaid'])->name('finance.unpaid.index');

    Route::get('/finance/incomes', [\App\Http\Controllers\FinanceController::class, 'incomes'])->name('finance.incomes.index');
    Route::post('/finance/incomes', [\App\Http\Controllers\FinanceController::class, 'storeIncome'])->name('finance.incomes.store');
    Route::delete('/finance/incomes/{transaction}', [\App\Http\Controllers\FinanceController::class, 'destroyIncome'])->name('finance.incomes.destroy');
    
    Route::get('/finance/profit', [\App\Http\Controllers\FinanceController::class, 'profit'])->name('finance.profit');
    Route::get('/finance/profit/export', [\App\Http\Controllers\FinanceController::class, 'exportProfit'])->name('finance.profit.export');

    // Shared Schedules (Master & Admin)
    Route::get('/admin/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('admin.schedules.index');
    Route::get('/admin/schedules/locations', [\App\Http\Controllers\Admin\ScheduleController::class, 'locationSchedules'])->name('admin.schedules.locations');
    Route::post('/admin/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('admin.schedules.store');
    Route::get('/admin/schedules/coach/{coach}/day/{day}', [\App\Http\Controllers\Admin\ScheduleController::class, 'showDay'])->name('admin.schedules.showDay');
    Route::put('/admin/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::delete('/admin/schedules/{schedule}', [\App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('admin.schedules.destroy');
    Route::post('/admin/schedules/{schedule}/assign', [\App\Http\Controllers\Admin\ScheduleController::class, 'assign'])->name('admin.schedules.assign');
    Route::delete('/admin/schedules/{schedule}/deassign/{student}', [\App\Http\Controllers\Admin\ScheduleController::class, 'deassign'])->name('admin.schedules.deassign');
    
    Route::put('/admin/availabilities/{availability}', [\App\Http\Controllers\Admin\ScheduleController::class, 'updateAvailability'])->name('admin.availabilities.update');
    Route::delete('/admin/availabilities/{availability}', [\App\Http\Controllers\Admin\ScheduleController::class, 'destroyAvailability'])->name('admin.availabilities.destroy');
});


// Admin Dashboard Group
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');


    // Admin Operations
    Route::get('/admin/operations/approvals', [\App\Http\Controllers\Admin\OperationalController::class, 'approvals'])->name('admin.operations.approvals');
    Route::get('/admin/operations/approvals/{scheduleRequest}', [\App\Http\Controllers\Admin\OperationalController::class, 'showApproval'])->name('admin.operations.showApproval');
    Route::post('/admin/operations/approvals/{scheduleRequest}', [\App\Http\Controllers\Admin\OperationalController::class, 'updateApproval'])->name('admin.operations.updateApproval');
    Route::delete('/admin/operations/approvals/{scheduleRequest}', [\App\Http\Controllers\Admin\OperationalController::class, 'destroyApproval'])->name('admin.operations.destroyApproval');

    // Admin Report Cards
    Route::get('/admin/report-cards', [\App\Http\Controllers\Admin\ReportCardController::class, 'index'])->name('admin.report-cards.index');
    Route::get('/admin/report-cards/{student}', [\App\Http\Controllers\Admin\ReportCardController::class, 'show'])->name('admin.report-cards.show');
    Route::put('/admin/report-cards/{student}/{attendance}/admin-note', [\App\Http\Controllers\Admin\ReportCardController::class, 'updateAdminNote'])->name('admin.report-cards.update-admin-note');

    // Admin Trials
    Route::resource('/admin/trials', \App\Http\Controllers\Admin\TrialController::class)->names([
        'index' => 'admin.trials.index',
        'create' => 'admin.trials.create',
        'store' => 'admin.trials.store',
        'edit' => 'admin.trials.edit',
        'update' => 'admin.trials.update',
        'destroy' => 'admin.trials.destroy',
    ]);

    
    // Payroll
    // Payroll
    Route::get('/finance/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('finance.payroll.index');
    Route::post('/finance/payroll/pay', [\App\Http\Controllers\PayrollController::class, 'paySalary'])->name('finance.payroll.pay');

    // E-Wallet
    Route::get('/wallets', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('admin.wallets.index');
    Route::get('/wallets/{user}', [\App\Http\Controllers\Admin\WalletController::class, 'show'])->name('admin.wallets.show');
    Route::post('/wallets/{user}/transaction', [\App\Http\Controllers\Admin\WalletController::class, 'storeTransaction'])->name('admin.wallets.transaction');
});

// Pelatih Dashboard Group
Route::middleware(['auth', 'role:pelatih'])->group(function () {
    Route::get('/pelatih/dashboard', function () {
        $coach = auth()->user();
        $classes = $coach->swimClasses()->with('students.user')->get();
        return view('pelatih.dashboard', compact('classes'));
    })->name('pelatih.dashboard');



    Route::get('/pelatih/schedules', [\App\Http\Controllers\Pelatih\ScheduleController::class, 'index'])->name('pelatih.schedules.index');
    Route::post('/pelatih/schedules', [\App\Http\Controllers\Pelatih\ScheduleController::class, 'store'])->name('pelatih.schedules.store');
    
    Route::get('/pelatih/all-schedules', [\App\Http\Controllers\Pelatih\ScheduleController::class, 'allSchedules'])->name('pelatih.all-schedules.index');
    
    // Pelatih Reports & Requests
    Route::get('/pelatih/reports', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'index'])->name('pelatih.reports.index');
    Route::get('/pelatih/requests', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'requestsIndex'])->name('pelatih.requests.index');
    Route::get('/pelatih/schedules/{schedule}/report', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'create'])->name('pelatih.reports.create');
    Route::post('/pelatih/schedules/{schedule}/report', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'store'])->name('pelatih.reports.store');
    
    Route::get('/pelatih/schedules/{schedule}/request', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'requestForm'])->name('pelatih.requests.create');
    Route::get('/pelatih/schedules/{schedule}/request-absent', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'requestAbsentForm'])->name('pelatih.requests.createAbsent');
    Route::post('/pelatih/schedules/{schedule}/request', [\App\Http\Controllers\Pelatih\TrainingReportController::class, 'submitRequest'])->name('pelatih.requests.store');
    
    Route::post('/pelatih/schedules/{schedule}/request-delete', [\App\Http\Controllers\Pelatih\ScheduleController::class, 'requestDelete'])->name('pelatih.schedules.requestDelete');
    
    Route::get('/pelatih/payroll-history', [\App\Http\Controllers\Pelatih\PayrollHistoryController::class, 'history'])->name('pelatih.payroll.history');

    // Pelatih Trials
    Route::get('/pelatih/trials', [\App\Http\Controllers\Pelatih\TrialController::class, 'index'])->name('pelatih.trials.index');
    Route::get('/pelatih/trials/{trial}/report', [\App\Http\Controllers\Pelatih\TrialController::class, 'edit'])->name('pelatih.trials.report');
    Route::put('/pelatih/trials/{trial}', [\App\Http\Controllers\Pelatih\TrialController::class, 'update'])->name('pelatih.trials.update');
});

// Murid Dashboard Group
Route::middleware(['auth', 'role:murid'])->group(function () {
    Route::get('/murid/dashboard', function () {
        $student = auth()->user()->student;
        $classes = collect();
        $schedules = collect();
        $activeInvals = collect();
        if ($student) {
            $classes = $student->swimClasses()->with('coaches.position')->get();
            
            // Get active inval requests for student's schedules
            $activeInvals = \App\Models\ScheduleRequest::with(['substituteCoach', 'schedule.poolLocation', 'proposedPoolLocation', 'schedule.coach'])
                ->where('type', 'inval')
                ->where('status', 'approved')
                ->where('proposed_date', '>=', now()->subDays(7)->format('Y-m-d'))
                ->whereHas('schedule.students', function ($query) use ($student) {
                    $query->where('students.id', $student->id);
                })
                ->whereDoesntHave('schedule.trainingReports', function ($query) {
                    $query->whereColumn('training_date', 'schedule_requests.proposed_date');
                })
                ->get();
                
            $schedules = $student->schedules()->with(['coach.position', 'poolLocation'])->orderByRaw("CASE day 
                WHEN 'Senin' THEN 1 
                WHEN 'Selasa' THEN 2 
                WHEN 'Rabu' THEN 3 
                WHEN 'Kamis' THEN 4 
                WHEN 'Jumat' THEN 5 
                WHEN 'Sabtu' THEN 6 
                WHEN 'Minggu' THEN 7 
                ELSE 8 END")->orderBy('start_time')->get();
        }
        return view('murid.dashboard', compact('classes', 'student', 'activeInvals', 'schedules'));
    })->name('murid.dashboard');


    Route::get('/murid/reports', [\App\Http\Controllers\StudentReportController::class, 'index'])->name('student.reports.index');
    Route::get('/murid/payments', [\App\Http\Controllers\StudentPaymentController::class, 'index'])->name('student.payments.index');
    Route::get('/murid/payments/create', [\App\Http\Controllers\StudentPaymentController::class, 'create'])->name('student.payments.create');
    Route::post('/murid/payments', [\App\Http\Controllers\StudentPaymentController::class, 'store'])->name('student.payments.store');
});

Route::middleware('auth')->group(function () {
    // Shared Receipt Access
    Route::get('/murid/payments/{transaction}/receipt', [\App\Http\Controllers\StudentPaymentController::class, 'receipt'])->name('student.payments.receipt');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
