<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user yang memiliki role 'mahasiswa'
        $mahasiswaUsers = User::where('role', 'mahasiswa')->get();

        foreach ($mahasiswaUsers as $user) {
            // Periksa apakah mahasiswa ini sudah ada di tabel students
            $existingStudent = Student::where('user_id', $user->id)->first();

            if (!$existingStudent) {
                // Ambil github_username dari tabel users
                $githubUsername = $user->github_username ?? 'Tidak Ditemukan';

                // Logging untuk debugging
                Log::info("Menambahkan mahasiswa ke tabel students:", [
                    'user_id' => $user->id,
                    'github_username' => $githubUsername,
                ]);

                // Buat entri baru di tabel students
                Student::create([
                    'user_id' => $user->id,
                    'github_username' => $githubUsername,
                    'class_id' => null,
                ]);

                echo "✅ Mahasiswa {$user->name} ditambahkan ke tabel students dengan GitHub Username: $githubUsername\n";
            } else {
                echo "⏩ Mahasiswa {$user->name} sudah ada di tabel students. Melewati...\n";
            }
        }

        echo "✅ Seeder StudentSeeder selesai!\n";
    }
}
