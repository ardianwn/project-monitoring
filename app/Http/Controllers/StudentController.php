<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('user', 'class');

        // Pencarian berdasarkan Nama, Email, atau NIM
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
            })->orWhere('nim', 'LIKE', "%$search%");
        }

        // Pengurutan berdasarkan kelas
        if ($request->has('class_id') && !empty($request->class_id)) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->get();
        $classes = ClassModel::all(); // Ambil semua kelas untuk dropdown filter

        return view('students.index', compact('students', 'classes'));
    }

    public function show($id)
    {
        $student = Student::with('user', 'class')->findOrFail($id);
        $repositories = []; 

        if ($student->github_username) {
            $githubUsername = $student->github_username;
            $githubToken = env('GITHUB_TOKEN'); 
            $headers = $githubToken ? ['Authorization' => "token $githubToken"] : [];

            try {
                $response = Http::withHeaders($headers)->get("https://api.github.com/users/$githubUsername/repos");

                if ($response->successful()) {
                    $repositories = collect($response->json())->map(function ($repo) use ($student) {
                        return [
                            'name' => $repo['name'],
                            'url' => route('students.repository.detail', ['id' => $student->id, 'repoName' => $repo['name']])
                        ];
                    })->toArray();
                }
            } catch (\Exception $e) {
                return view('students.show', compact('student', 'repositories'))
                    ->with('error', 'Gagal mengambil data repository dari GitHub.');
            }
        }

        return view('students.show', compact('student', 'repositories'));
    }

    public function repositoryDetail($id, $repoName)
    {
        $student = Student::with('user')->findOrFail($id);
        $githubUsername = $student->github_username;
        $githubToken = env('GITHUB_TOKEN'); 
        $headers = $githubToken ? ['Authorization' => "token $githubToken"] : [];
    
        $repoInfo = [];
        $commits = [];
        $issues = [];
        $pullRequests = [];
        $languages = [];
        $contributors = [];
        $commitStats = [];
    
        if ($githubUsername) {
            try {
                // **1. Ambil Informasi Repository**
                $repoResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName");
    
                if ($repoResponse->successful()) {
                    $repoInfo = $repoResponse->json();
                }
    
                // **2. Ambil Riwayat Commit & Detail Commit untuk Lines Added & Deleted**
                $commitResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName/commits");
    
                if ($commitResponse->successful()) {
                    $commits = collect($commitResponse->json())->map(function ($commit) use ($headers, $githubUsername, $repoName) {
                        $commitSha = $commit['sha'];
    
                        // Ambil detail commit per SHA
                        $commitDetailResponse = Http::withHeaders($headers)
                            ->get("https://api.github.com/repos/$githubUsername/$repoName/commits/$commitSha");
    
                        $additions = 0;
                        $deletions = 0;
    
                        if ($commitDetailResponse->successful()) {
                            $commitDetail = $commitDetailResponse->json();
                            $additions = $commitDetail['stats']['additions'] ?? 0;
                            $deletions = $commitDetail['stats']['deletions'] ?? 0;
                        }
    
                        return [
                            'sha' => $commitSha,
                            'message' => $commit['commit']['message'],
                            'author' => $commit['commit']['author']['name'],
                            'date' => $commit['commit']['author']['date'],
                            'url' => $commit['html_url'],
                            'additions' => $additions,
                            'deletions' => $deletions,
                        ];
                    })->toArray();
                }
    
                // **3. Ambil Issues**
                $issuesResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName/issues");
    
                if ($issuesResponse->successful()) {
                    $issues = collect($issuesResponse->json())->map(function ($issue) {
                        return [
                            'title' => $issue['title'],
                            'status' => $issue['state'],
                            'creator' => $issue['user']['login'],
                            'labels' => collect($issue['labels'])->pluck('name')->toArray(),
                        ];
                    })->toArray();
                }
    
                // **4. Ambil Pull Requests**
                $pullResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName/pulls");
    
                if ($pullResponse->successful()) {
                    $pullRequests = collect($pullResponse->json())->map(function ($pr) {
                        return [
                            'title' => $pr['title'],
                            'status' => $pr['state'],
                            'creator' => $pr['user']['login'],
                        ];
                    })->toArray();
                }
    
                // **5. Ambil Statistik Kode (Bahasa)**
                $languagesResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName/languages");
    
                if ($languagesResponse->successful()) {
                    $languages = $languagesResponse->json();
                }
    
                // **6. Ambil Statistik Kontributor**
                $contributorsResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName/contributors");
    
                if ($contributorsResponse->successful()) {
                    $contributors = collect($contributorsResponse->json())->map(function ($contributor) {
                        return [
                            'username' => $contributor['login'],
                            'commits' => $contributor['contributions'],
                        ];
                    })->toArray();
                }
    
                // **7. Ambil Statistik Commit Per Hari**
                $commitStatsResponse = Http::withHeaders($headers)
                    ->get("https://api.github.com/repos/$githubUsername/$repoName/stats/commit_activity");
    
                if ($commitStatsResponse->successful()) {
                    $commitStats = collect($commitStatsResponse->json())->mapWithKeys(function ($week) {
                        return [date('Y-m-d', $week['week']) => $week['total']];
                    })->toArray();
                }
    
            } catch (\Exception $e) {
                return view('students.repository-detail', compact(
                    'student', 'repoName', 'repoInfo', 'commits', 'issues',
                    'pullRequests', 'languages', 'contributors', 'commitStats'
                ))->with('error', 'Gagal mengambil data dari GitHub.');
            }
        }
    
        return view('students.repository-detail', compact(
            'student', 'repoName', 'repoInfo', 'commits', 'issues',
            'pullRequests', 'languages', 'contributors', 'commitStats'
        ));
    }    
}
