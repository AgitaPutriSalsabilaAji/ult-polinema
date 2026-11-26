<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    public function showLoginForm(): RedirectResponse
    {
        return redirect()->route('beranda');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->session()->flash('auth_modal', 'login');

        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
            'layanan_id' => ['nullable', 'integer'],
        ]);

        $credentials['email'] = Str::lower($credentials['email']);

        if (! Auth::guard('web')->attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            $request->boolean('remember')
        )) {
            return back()
                ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])
                ->onlyInput('email', 'layanan_id');
        }

        $request->session()->regenerate();
        $request->session()->forget('auth_modal');

        return $this->redirectAfterAuthentication($request);
    }

    public function showRegistrationForm(): RedirectResponse
    {
        return redirect()->route('beranda');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->session()->flash('auth_modal', 'register');
        $request->merge([
            'email' => Str::lower((string) $request->input('email')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'layanan_id' => ['nullable', 'integer'],
        ]);

        $data['name'] = trim($data['name']);
        $data['email'] = Str::lower($data['email']);
        $data['password'] = Hash::make($data['password']);
        unset($data['layanan_id']);

        $user = User::create($data);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        $request->session()->forget('auth_modal');

        return $this->redirectAfterAuthentication($request);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }

    private function redirectAfterAuthentication(Request $request): RedirectResponse
    {
        $serviceId = $request->input('layanan_id');

        if ($serviceId !== null && Layanan::query()
            ->where('status', 'aktif')
            ->whereKey($serviceId)
            ->exists()) {
            return redirect()->route('pengajuan.form', ['layanan' => $serviceId]);
        }

        return redirect()->intended(route('beranda'));
    }
}
