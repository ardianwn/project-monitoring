<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-4">
                <div class="dark:bg-gray-800 dark:text-gray-400 shadow-md sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold">Total Mahasiswa</h3>
                    <p class="text-2xl">{{ $totalMahasiswa }}</p>
                </div>
                <div class="dark:bg-gray-800 dark:text-gray-400 shadow-md sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold">Total Commit Hari Ini</h3>
                    <p class="text-2xl">{{ $totalCommits }}</p>
                </div>
                <div class="dark:bg-gray-800 dark:text-gray-400 shadow-md sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold">Total Kelas</h3> <!-- Ubah dari "Total Laporan" ke "Total Kelas" -->
                    <p class="text-2xl">{{ $totalClasses }}</p> <!-- Variabel diubah -->
                </div>
            </div>

            <div class="mt-6 dark:bg-gray-800 dark:text-gray-400 shadow-md sm:rounded-lg p-6">
                <h3 class="text-lg font-bold">Statistik Aktivitas Mahasiswa</h3>
                <canvas id="adminChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('adminChart').getContext('2d');
        const labels = @json($labels);
        const data = @json($data);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, // Nama mahasiswa
                datasets: [{
                    label: 'Jumlah Commit Bulanan',
                    data: data, // Jumlah commit
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
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
