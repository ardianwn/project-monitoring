<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class GitHubHelper
{
    /**
     * Mengambil GitHub Username berdasarkan email.
     */
    public static function getGitHubUsername($email)
    {
        $response = Http::get("https://api.github.com/search/users?q=$email+in:email");

        if ($response->successful() && isset($response->json()['items'][0])) {
            return $response->json()['items'][0]['login'];
        }

        return null; // Jika tidak ditemukan
    }
}
