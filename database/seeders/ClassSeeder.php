<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    public function run()
    {
        $classes = [];

        // Loop untuk membuat kelas dari T1A hingga T4J
        for ($level = 1; $level <= 4; $level++) {
            for ($section = 'A'; $section <= 'J'; $section++) {
                $classes[] = ['name' => "T{$level}{$section}", 'created_at' => now(), 'updated_at' => now()];
            }
        }

        // Insert ke database
        DB::table('classes')->insert($classes);

        echo "✅ Seeder berhasil dijalankan! Kelas dari T1A hingga T4J telah ditambahkan.\n";
    }
}
