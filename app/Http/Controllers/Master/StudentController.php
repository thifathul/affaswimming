<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::where(function($q) {
            $q->doesntHave('user')
              ->orWhereHas('user', function($q2) {
                  $q2->where('status', '!=', 'pending')->orWhereNull('status');
              });
        })->with(['user', 'swimClasses', 'schedules.coach'])->latest()->get();
        return view('master.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $swimClasses = \App\Models\SwimClass::where('status', 'aktif')->get();
        return view('master.students.create', compact('swimClasses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'birth_place_date' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:100',
            'school' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'swim_class_ids' => 'nullable|array',
            'swim_class_ids.*' => 'exists:swim_classes,id',
        ]);

        $userId = null;

        if ($request->filled('email')) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'murid',
                'status' => 'approved',
            ]);
            $userId = $user->id;
        }

        $student = Student::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'birth_place_date' => $request->birth_place_date,
            'age' => $request->age,
            'school' => $request->school,
            'user_id' => $userId,
        ]);

        if ($request->has('swim_class_ids')) {
            $student->swimClasses()->sync($request->swim_class_ids);
        }

        return redirect()->route('master.students.index')->with('success', 'Data murid berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $swimClasses = \App\Models\SwimClass::where('status', 'aktif')->get();
        $selectedClasses = $student->swimClasses->pluck('id')->toArray();
        return view('master.students.edit', compact('student', 'swimClasses', 'selectedClasses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'birth_place_date' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:100',
            'school' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'email' => 'nullable|email|max:255|unique:users,email,' . ($student->user_id ?? 'NULL'),
            'swim_class_ids' => 'nullable|array',
            'swim_class_ids.*' => 'exists:swim_classes,id',
        ]);

        $userId = $student->user_id;

        if ($request->filled('email')) {
            if ($student->user) {
                // Update existing user email
                $student->user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
            } else {
                // Create a new user since they didn't have one
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                    'role' => 'murid',
                    'status' => 'approved',
                ]);
                $userId = $user->id;
            }
        } else {
            // If email is emptied, we detach the user
            $userId = null;
        }

        $student->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'birth_place_date' => $request->birth_place_date,
            'age' => $request->age,
            'school' => $request->school,
            'status' => $request->status,
            'user_id' => $userId,
        ]);

        if ($request->has('swim_class_ids')) {
            $student->swimClasses()->sync($request->swim_class_ids);
        } else {
            $student->swimClasses()->detach();
        }

        return redirect()->route('master.students.index')->with('success', 'Data murid berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('master.students.index')->with('success', 'Data murid berhasil dihapus.');
    }

    /**
     * Toggle the active status of the student.
     */
    public function toggleStatus(Student $student)
    {
        $student->update([
            'status' => $student->status === 'aktif' ? 'nonaktif' : 'aktif'
        ]);

        $message = $student->status === 'aktif' ? 'Status murid berhasil diaktifkan.' : 'Status murid berhasil dinonaktifkan.';
        
        return redirect()->route('master.students.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $fileName = "Template_Import_Murid.csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Nama Siswa', 'Jenis Kelamin (Laki-laki/Perempuan)', 'Tempat, Tanggal Lahir', 'Usia', 'Sekolah', 'Email (Opsional)');

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); // BOM for Excel
            fputcsv($file, $columns, ';');
            fputcsv($file, ['Budi Santoso', 'Laki-laki', 'Bandung, 12 Agustus 2010', '10', 'SDN 1 Bandung', 'budi@example.com'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importData(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('import_file');
        $handle = fopen($file->getRealPath(), "r");
        
        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = true;
        $successCount = 0;
        
        while (($raw_string = fgets($handle)) !== false) {
            if ($header) {
                $header = false;
                continue;
            }
            
            if (trim($raw_string) == '') continue;

            $delimiter = strpos($raw_string, ';') !== false ? ';' : ',';
            $data = str_getcsv($raw_string, $delimiter);

            if (count($data) >= 5) {
                $name = trim($data[0]);
                $gender = trim($data[1]);
                $birthPlaceDate = trim($data[2]);
                $age = trim($data[3]);
                $school = trim($data[4]);
                $email = isset($data[5]) ? trim($data[5]) : null;

                if (empty($name) || empty($birthPlaceDate) || empty($age) || empty($school)) {
                    continue;
                }

                $userId = null;
                if (!empty($email)) {
                    $existingUser = User::where('email', $email)->first();
                    if (!$existingUser) {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                            'role' => 'murid',
                            'status' => 'approved',
                        ]);
                        $userId = $user->id;
                    } else {
                        $userId = $existingUser->id;
                    }
                }

                Student::create([
                    'name' => $name,
                    'gender' => in_array(ucfirst(strtolower($gender)), ['Laki-laki', 'Perempuan']) ? ucfirst(strtolower($gender)) : null,
                    'birth_place_date' => $birthPlaceDate,
                    'age' => is_numeric($age) ? $age : null,
                    'school' => $school,
                    'user_id' => $userId,
                ]);

                $successCount++;
            }
        }
        
        fclose($handle);

        return redirect()->route('master.students.index')->with('success', "$successCount data murid berhasil diimport.");
    }

    public function exportData()
    {
        $fileName = "Data_Murid_" . date('Ymd_His') . ".csv";
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $students = Student::with(['user', 'swimClasses', 'schedules.coach'])->get();
        $columns = array('ID', 'Nama Siswa', 'Jenis Kelamin', 'Tempat, Tanggal Lahir', 'Usia', 'Sekolah', 'Status', 'Email', 'Kelas Berenang', 'Pelatih');

        $callback = function() use($students, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); // BOM for Excel
            fputcsv($file, $columns, ';');

            foreach ($students as $student) {
                $swimClasses = $student->swimClasses->pluck('name')->implode(', ');
                $coaches = $student->schedules->pluck('coach.name')->unique()->implode(', ');
                $email = $student->user ? $student->user->email : '';

                fputcsv($file, [
                    'AFFA-M-' . str_pad($student->id, 4, '0', STR_PAD_LEFT),
                    $student->name,
                    $student->gender ?? '-',
                    $student->birth_place_date ?? '-',
                    $student->age ?? '-',
                    $student->school ?? '-',
                    $student->status,
                    $email,
                    $swimClasses,
                    $coaches
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
