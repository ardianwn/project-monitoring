<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return redirect()->route($user->role === 'admin' ? 'dashboard.admin' : 'dashboard.mahasiswa');
    })->name('dashboard');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/dashboard/mahasiswa', [DashboardController::class, 'mahasiswa'])
        ->middleware('role:mahasiswa')
        ->name('dashboard.mahasiswa');
});


// Middleware untuk pengguna yang sudah login
Route::middleware(['auth'])->group(function () {

    // Redirect berdasarkan role saat mengakses /students
    Route::get('/students', function () {
        $user = Auth::user();
        return redirect()->route($user->role === 'admin' ? 'students.index' : 'dashboard');
    });

    // Hanya admin yang bisa mengakses manajemen mahasiswa
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('students', StudentController::class);

        // Route tambahan untuk edit, update, dan delete
        Route::get('students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    });});

    Route::get('students/{id}/repository/{repoName}', [StudentController::class, 'repositoryDetail'])
    ->name('students.repository.detail');


// Profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Login dengan GitHub
Route::get('/auth/github', [GitHubController::class, 'redirect'])->name('github.login');
Route::get('/auth/github/callback', [GitHubController::class, 'callback']);

require __DIR__.'/auth.php';
