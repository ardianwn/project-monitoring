<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Student;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'github_username',
    ];

    /**
     * Attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Boot method for model event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Saat user mahasiswa dibuat, otomatis masukkan ke tabel students
        static::created(function ($user) {
            if ($user->role === 'mahasiswa') {
                $githubUsername = self::getGitHubUsername($user->email, $user->github_username);

                Student::create([
                    'user_id'         => $user->id,
                    'github_username' => $githubUsername ?? 'Tidak Ditemukan',
                    'class_id'        => null,
                ]);

                Log::info("✅ Mahasiswa baru ditambahkan ke tabel students:", [
                    'user_id'         => $user->id,
                    'github_username' => $githubUsername,
                ]);
            }
        });

        // Saat user mahasiswa diperbarui, perbarui juga tabel students
        static::updated(function ($user) {
            if ($user->role === 'mahasiswa') {
                $student = Student::where('user_id', $user->id)->first();
                if ($student) {
                    $student->update([
                        'github_username' => $user->github_username,
                    ]);

                    Log::info("✅ Mahasiswa diperbarui di tabel students:", [
                        'user_id'         => $user->id,
                        'github_username' => $user->github_username,
                    ]);
                }
            }
        });
    }

    /**
     * Relationship: User has one Student.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a mahasiswa.
     */
    public function isMahasiswa()
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Fetch GitHub username using GitHub API.
     * Prioritas:
     * 1. Jika sudah ada di database (dari `users.github_username`), gunakan itu.
     * 2. Jika tidak ada, coba ambil dari API GitHub berdasarkan email.
     * 3. Jika tidak ditemukan di GitHub, kembalikan 'Tidak Ditemukan'.
     */
    public static function getGitHubUsername($email, $defaultUsername = null)
    {
        if (!empty($defaultUsername)) {
            return $defaultUsername; // Jika sudah ada di database, langsung pakai
        }

        $response = Http::get("https://api.github.com/search/users?q=$email+in:email");

        if ($response->successful() && isset($response->json()['items'][0])) {
            return $response->json()['items'][0]['login'];
        }

        return 'Tidak Ditemukan'; // Jika tetap tidak ditemukan
    }
}
