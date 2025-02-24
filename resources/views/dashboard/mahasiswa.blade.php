<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-400 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Daftar Repository Anda</h3>

                @if (!empty($repositories))
                    <ul class="list-disc pl-6">
                        @foreach ($repositories as $repo)
                            <li>
                                <a href="{{ $repo['url'] }}" target="_blank" 
                                   class="border border-blue-500 text-blue-500 px-3 py-1 rounded hover:bg-blue-500 hover:text-white transition">
                                    {{ $repo['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Anda belum memiliki repository di GitHub.</p>
                @endif
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 text-gray-400 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold">Grafik Jumlah Edit (Commit) dalam 30 Hari Terakhir</h3>
                <canvas id="commitChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('commitChart').getContext('2d');

        const labels = @json(array_keys($commitData));
        const data = @json(array_values($commitData));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Commit per Hari',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
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
