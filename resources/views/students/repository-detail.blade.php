<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Detail Repository: ') }} {{ $repoName }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-400 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Informasi Umum Repository -->
                <h3 class="text-lg font-bold">Informasi Repository</h3>
                <p><strong>Deskripsi:</strong> {{ $repoInfo['description'] ?? 'Tidak ada deskripsi' }}</p>
                <p><strong>Bahasa:</strong> {{ implode(', ', array_keys($languages)) }}</p>
                <p><strong>Stars:</strong> {{ $repoInfo['stargazers_count'] ?? 0 }}</p>
                <p><strong>Forks:</strong> {{ $repoInfo['forks_count'] ?? 0 }}</p>
                <p><strong>Issues:</strong> {{ $repoInfo['open_issues_count'] ?? 0 }}</p>

                <!-- Riwayat Commit -->
                <h3 class="text-lg font-bold mt-6">Riwayat Commit</h3>
                    <table class="table-auto w-full border-collapse border border-gray-400">
                        <thead>
                            <tr class="bg-gray-800">
                                <th class="border px-4 py-2">SHA</th>
                                <th class="border px-4 py-2">Pesan Commit</th>
                                <th class="border px-4 py-2">Author</th>
                                <th class="border px-4 py-2">Tanggal</th>
                                <th class="border px-4 py-2">Lines Added</th>
                                <th class="border px-4 py-2">Lines Deleted</th>
                                <th class="border px-4 py-2">Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commits as $commit)
                                <tr>
                                    <td class="border px-4 py-2">{{ Str::limit($commit['sha'], 8) }}</td>
                                    <td class="border px-4 py-2">{{ $commit['message'] }}</td>
                                    <td class="border px-4 py-2">{{ $commit['author'] }}</td>
                                    <td class="border px-4 py-2">{{ $commit['date'] }}</td>
                                    <td class="border px-4 py-2 text-green-500">+{{ $commit['additions'] }}</td>
                                    <td class="border px-4 py-2 text-red-500">-{{ $commit['deletions'] }}</td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ $commit['url'] }}" target="_blank" class="text-blue-500 hover:underline">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                <!-- Issues -->
                <h3 class="text-lg font-bold mt-6">Issues</h3>
                @if (count($issues) > 0)
                    <ul class="list-disc pl-6">
                        @foreach ($issues as $issue)
                            <li>
                                <strong>{{ $issue['title'] }}</strong> - {{ $issue['status'] }} - {{ $issue['creator'] }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Tidak ada issue.</p>
                @endif

                <!-- Pull Requests -->
                <h3 class="text-lg font-bold mt-6">Pull Requests</h3>
                @if (count($pullRequests) > 0)
                    <ul class="list-disc pl-6">
                        @foreach ($pullRequests as $pr)
                            <li>
                                <strong>{{ $pr['title'] }}</strong> - {{ $pr['status'] }} - {{ $pr['creator'] }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Tidak ada pull request.</p>
                @endif

                <!-- Statistik Commit Per Hari -->
                <h3 class="text-lg font-bold mt-6">Statistik Commit Per Hari</h3>
                <canvas id="commitChart" class="mt-4"></canvas>

                <a href="{{ route('students.show', $student->id) }}" 
                   class="border border-gray-500 text-gray-500 px-4 py-2 rounded hover:bg-gray-500 hover:text-white transition mt-6 inline-block">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('commitChart').getContext('2d');
        const labels = @json(array_keys($commitStats));
        const data = @json(array_values($commitStats));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Commit',
                    data: data,
                    fill: false,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
</x-app-layout>
