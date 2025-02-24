<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repository;
use App\Models\Student;
use GuzzleHttp\Client;

class RepositoryController extends Controller
{
    public function index()
    {
        $repositories = Repository::with('student')->get();
        return view('repositories.index', compact('repositories'));
    }

    public function show($id)
    {
        $repository = Repository::with('student')->findOrFail($id);
        return view('repositories.show', compact('repository'));
    }

    public function create()
    {
        $students = Student::all();
        return view('repositories.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required',
            'url' => 'required|url',
        ]);

        Repository::create($request->all());

        return redirect()->route('repositories.index')->with('success', 'Repository created successfully.');
    }

    public function edit($id)
    {
        $repository = Repository::findOrFail($id);
        $students = Student::all();
        return view('repositories.edit', compact('repository', 'students'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required',
            'url' => 'required|url',
        ]);

        $repository = Repository::findOrFail($id);
        $repository->update($request->all());

        return redirect()->route('repositories.index')->with('success', 'Repository updated successfully.');
    }

    public function destroy($id)
    {
        $repository = Repository::findOrFail($id);
        $repository->delete();

        return redirect()->route('repositories.index')->with('success', 'Repository deleted successfully.');
    }

    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchRepositories(Student $student)
    {
        $response = $this->client->request('GET', "https://api.github.com/users/{$student->github_username}/repos");
        $repositories = json_decode($response->getBody(), true);

        foreach ($repositories as $repo) {
            Repository::updateOrCreate(
                ['name' => $repo['name'], 'student_id' => $student->id],
                ['url' => $repo['html_url']]
            );
        }
    }

    public function fetchCommits(Student $student, $repositoryName)
    {
        $response = $this->client->request('GET', "https://api.github.com/repos/{$student->github_username}/{$repositoryName}/commits");
        $commits = json_decode($response->getBody(), true);

        // Process commits and update database
        foreach ($commits as $commit) {
            // Example: Save commit data to a commits table
            // Commit::updateOrCreate(
            //     ['sha' => $commit['sha'], 'repository_id' => $repository->id],
            //     [
            //         'message' => $commit['commit']['message'],
            //         'author' => $commit['commit']['author']['name'],
            //         'date' => $commit['commit']['author']['date'],
            //     ]
            // );
        }
    }
}