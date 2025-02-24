<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ClassModel;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalClasses = ClassModel::count(); // Ambil jumlah kelas
        $totalCommits = 0;

        // Ambil data mahasiswa dan username GitHub mereka dari database
        $mahasiswa = User::where('role', 'mahasiswa')->get(['name', 'github_username']);

        $labels = [];
        $data = [];

        // Token GitHub dari .env
        $githubToken = env('GITHUB_TOKEN');

        if (!$githubToken) {
            return view('dashboard.admin', compact('totalMahasiswa', 'totalClasses', 'totalCommits', 'labels', 'data'))
                ->with('error', 'GitHub Token tidak ditemukan.');
        }

        try {
            foreach ($mahasiswa as $mhs) {
                $username = $mhs->github_username;
                $commitCount = 0;

                if ($username) {
                    // Ambil daftar repository mahasiswa dari GitHub
                    $reposResponse = Http::withToken($githubToken)
                        ->get("https://api.github.com/users/$username/repos");

                    if ($reposResponse->successful() && is_array($reposResponse->json())) {
                        $repositories = collect($reposResponse->json())->pluck('name')->toArray();

                        // Ambil jumlah commit dari setiap repository mahasiswa
                        foreach ($repositories as $repo) {
                            $commitsResponse = Http::withToken($githubToken)
                                ->get("https://api.github.com/repos/$username/$repo/commits", [
                                    'since' => now()->subDays(30)->toIso8601String(),
                                    'until' => now()->toIso8601String(),
                                ]);

                            if ($commitsResponse->successful() && is_array($commitsResponse->json())) {
                                $commitCount += count($commitsResponse->json());
                            }
                        }
                    }
                }

                // Simpan ke array labels dan data untuk Chart.js
                $labels[] = $mhs->name;
                $data[] = $commitCount;
                $totalCommits += $commitCount;
            }
        } catch (\Exception $e) {
            return view('dashboard.admin', compact('totalMahasiswa', 'totalClasses', 'totalCommits', 'labels', 'data'))
                ->with('error', 'Gagal mengambil data dari GitHub API. Error: ' . $e->getMessage());
        }

        return view('dashboard.admin', compact('totalMahasiswa', 'totalClasses', 'totalCommits', 'labels', 'data'));
    }

    public function mahasiswa()
    {
        $user = Auth::user(); // ✅ Perbaiki dengan Auth::user() agar dikenali Laravel
        $githubUsername = $user->github_username;
        $repositories = [];
        $commitData = [];

        $githubToken = env('GITHUB_TOKEN');
        $headers = $githubToken ? ['Authorization' => "token $githubToken"] : [];

        if ($githubUsername) {
            try {
                // Ambil daftar repository dari GitHub API
                $reposResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/users/$githubUsername/repos");

                if ($reposResponse->successful()) {
                    $repositories = collect($reposResponse->json())->map(function ($repo) {
                        return [
                            'name' => $repo['name'],
                            'url' => $repo['html_url'],
                        ];
                    })->toArray();

                    // Ambil data commit dalam 30 hari terakhir
                    $dates = collect(range(0, 29))->map(function ($i) {
                        return now()->subDays($i)->format('Y-m-d');
                    })->reverse()->values();

                    $commitData = $dates->mapWithKeys(function ($date) {
                        return [$date => 0]; // Inisialisasi semua tanggal dengan 0 commit
                    })->toArray();

                    foreach ($repositories as $repo) {
                        $commitsResponse = Http::withHeaders($headers)
                            ->get("https://api.github.com/repos/$githubUsername/{$repo['name']}/commits", [
                                'since' => now()->subDays(30)->toIso8601String(),
                                'until' => now()->toIso8601String(),
                            ]);

                        if ($commitsResponse->successful()) {
                            foreach ($commitsResponse->json() as $commit) {
                                $commitDate = substr($commit['commit']['committer']['date'], 0, 10); // Ambil tanggal saja
                                if (isset($commitData[$commitDate])) {
                                    $commitData[$commitDate]++;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                return view('dashboard.mahasiswa', compact('repositories', 'commitData'))
                    ->with('error', 'Gagal mengambil data dari GitHub API.');
            }
        }

        return view('dashboard.mahasiswa', compact('repositories', 'commitData'));
    }    
}
