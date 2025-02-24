<?php

return [

'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // Pastikan semua path yang diperlukan diizinkan

'allowed_methods' => ['*'], // Izinkan semua metode HTTP (GET, POST, PUT, DELETE, dsb.)

'allowed_origins' => ['http://monitoring.tivokasiub.cloud', 'http://localhost', 'http://127.0.0.1'], 
// Bisa menggunakan '*' untuk mengizinkan semua domain, tapi lebih aman spesifik ke domain yang diperlukan.

'allowed_origins_patterns' => [], // Tidak perlu diisi jika `allowed_origins` sudah mencakup domain yang diperlukan

'allowed_headers' => ['*'], // Mengizinkan semua header

'exposed_headers' => [],

'max_age' => 0,

'supports_credentials' => true, // Jika menggunakan autentikasi berbasis sesi atau token

];
