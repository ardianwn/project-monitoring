<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Route::aliasMiddleware('role', RoleMiddleware::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('auth')->group(function () {
            Route::get('/dashboard', function () {
                $user = Auth::user();

                if ($user->role === 'admin') {
                    return redirect()->route('dashboard.admin');
                } elseif ($user->role === 'mahasiswa') {
                    return redirect()->route('dashboard.mahasiswa');
                }

                return redirect('/');
            });
        });
    }
}
