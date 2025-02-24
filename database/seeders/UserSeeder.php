<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Data pengguna awal
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'github_username' => null, // Admin tidak punya GitHub Username
            ],
            [
                'name' => 'Ardian Wahyu Nizar',
                'email' => 'ardianwah614@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ],
        ];

        // Masukkan data ke database
        foreach ($users as $userData) {
            // Jika user adalah mahasiswa, cari GitHub Username dari API
            if ($userData['role'] === 'mahasiswa') {
                $githubUsername = $this->getGitHubUsername($userData['email']);
                $userData['github_username'] = $githubUsername ?? 'Tidak Ditemukan';
            } else {
                // Admin atau user lain tidak memiliki GitHub Username
                $userData['github_username'] = null;
            }

            // Insert or update user
            User::updateOrCreate(
                ['email' => $userData['email']], // Hindari duplikasi email
                $userData
            );

            echo "✅ User {$userData['name']} dengan GitHub Username: " . ($userData['github_username'] ?? 'Tidak Ada') . " berhasil ditambahkan!\n";
        }
    }

    /**
     * Fungsi untuk mengambil GitHub Username berdasarkan email.
     */
    private function getGitHubUsername($email)
    {
        // Menggunakan API GitHub untuk mencari user berdasarkan email
        $response = Http::get("https://api.github.com/search/users?q=$email+in:email");

        if ($response->successful() && isset($response->json()['items'][0]['login'])) {
            return $response->json()['items'][0]['login'];
        }

        return null; // Jika tidak ditemukan
    }
}
