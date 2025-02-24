<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Redirect ke halaman login GitHub.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Handle callback setelah pengguna login dengan GitHub.
     */
    public function handleProviderCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal mengautentikasi dengan GitHub.');
        }

        // Jika email GitHub tidak tersedia, buat email dummy
        $email = $githubUser->getEmail() ?? $githubUser->getId() . '@github.com';

        // Periksa apakah pengguna sudah ada
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $email,
                'password' => Hash::make(Str::random(60)),
                'github_username' => $githubUser->getNickname(),
                'role' => 'mahasiswa', // Secara default, semua pengguna GitHub adalah mahasiswa
            ]
        );

        Auth::login($user);

        // Jika mahasiswa belum ada di tabel students, buat entri baru
        Student::updateOrCreate(
            ['user_id' => $user->id],
            ['github_username' => $githubUser->getNickname()]
        );

        return redirect()->route('dashboard');
    }

    /**
     * Logout pengguna.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
