<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $poolLocations = \App\Models\PoolLocation::all()->unique(function ($item) {
            return $item->name . '-' . $item->meeting_count;
        });
        return view('auth.register', compact('poolLocations'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Also sanitize amount before validation
        $request->merge([
            'amount' => $request->amount ? str_replace('.', '', $request->amount) : null,
        ]);

        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'birth_place_date' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1'],
            'school' => ['nullable', 'string', 'max:255'],
            
            // Package purchase validation
            'pool_location_id' => 'required|exists:pool_locations,id',
            'class_type' => 'required|in:private,semi_private',
            'amount' => 'required|numeric|min:0',
            'practice_start_date' => 'required|date',
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'murid',
                'status' => 'approved', // Automatically approved because they are buying a package
            ]);

            $student = \App\Models\Student::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'gender' => $request->gender,
                'birth_place_date' => $request->birth_place_date,
                'age' => $request->age,
                'school' => $request->school,
            ]);

            // Handle file upload
            $proofPath = null;
            if ($request->hasFile('proof_of_payment')) {
                $proofPath = $request->file('proof_of_payment')->store('payments', 'public');
            }

            \App\Models\Transaction::create([
                'student_id' => $student->id,
                'pool_location_id' => $request->pool_location_id,
                'class_type' => $request->class_type,
                'amount' => $request->amount,
                'practice_start_date' => $request->practice_start_date,
                'proof_of_payment' => $proofPath,
                'status' => 'pending',
            ]);

            \Illuminate\Support\Facades\DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect()->route('murid.dashboard')->with('success', 'Pendaftaran berhasil! Bukti pembayaran Anda sedang menunggu persetujuan Admin.');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pendaftaran. Silakan coba lagi.');
        }
    }
}
