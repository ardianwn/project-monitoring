<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    // Redirect ke GitHub
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    // Callback dari GitHub
    public function callback()
    {
        $githubUser = Socialite::driver('github')->user();

        // Cari atau buat user baru
        $user = User::updateOrCreate(
            ['email' => $githubUser->getEmail()],
            [
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'password' => bcrypt(str()->random(24)), // Password acak karena tidak digunakan
            ]
        );

        Auth::login($user);

        return redirect()->route($user->role === 'admin' ? 'dashboard.admin' : 'dashboard.mahasiswa');
    }
}
