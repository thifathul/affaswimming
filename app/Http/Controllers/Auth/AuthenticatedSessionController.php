<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $role = $request->user()->role;
        $user = $request->user();

        if ($role === 'murid') {

            if ($user->status === 'rejected') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'Pendaftaran Akun Anda ditolak oleh Admin.',
                ]);
            }

            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if ($student && $student->status === 'nonaktif') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.',
                ]);
            }
        }

        switch ($role) {
            case 'master':
                return redirect()->intended(route('master.dashboard', absolute: false));
            case 'admin':
                return redirect()->intended(route('admin.dashboard', absolute: false));
            case 'pelatih':
                return redirect()->intended(route('pelatih.dashboard', absolute: false));
            case 'murid':
            default:
                return redirect()->intended(route('murid.dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
