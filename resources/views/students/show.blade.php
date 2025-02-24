<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Detail Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-400 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Informasi Mahasiswa</h3>

                <p><strong>Nama:</strong> {{ optional($student->user)->name }}</p>
                <p><strong>Email:</strong> {{ optional($student->user)->email }}</p>
                <p><strong>NIM:</strong> {{ $student->nim ?? 'Belum Ada NIM' }}</p>
                <p><strong>GitHub Username:</strong> {{ $student->github_username ?? 'Tidak Ada Username' }}</p>
                <p><strong>Kelas:</strong> {{ optional($student->class)->name ?? 'Belum Ada Kelas' }}</p>

                <h3 class="text-lg font-bold mt-6">Repository Mahasiswa</h3>

                    @if (!empty($repositories))
                        <ul class="list-disc pl-6">
                            @foreach ($repositories as $repo)
                                <li>
                                    <a href="{{ route('students.repository.detail', ['id' => $student->id, 'repoName' => $repo['name']]) }}" 
                                    class="text-blue-500 hover:underline border border-blue-500 px-3 py-1 rounded hover:bg-blue-500 hover:text-white transition">
                                        {{ $repo['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Mahasiswa ini belum memiliki repository di GitHub.</p>
                    @endif

                <h3 class="text-lg font-bold mt-6">Jumlah Commit Per Hari</h3>

                @if (!empty($commitsPerDay))
                    <table class="min-w-full border border-gray-300 dark:border-gray-700 mt-4">
                        <thead>
                            <tr class="border-b bg-gray-100 dark:bg-gray-700">
                                <th class="py-2 px-4 border-r">Tanggal</th>
                                <th class="py-2 px-4">Jumlah Commit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commitsPerDay as $date => $count)
                                <tr class="border-b">
                                    <td class="py-2 px-4 border-r">{{ $date }}</td>
                                    <td class="py-2 px-4">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 mt-2">Mahasiswa ini belum melakukan commit dalam 7 hari terakhir.</p>
                @endif

                <div class="mt-6">
                    <a href="{{ route('students.index') }}" 
                       class="border border-gray-500 text-gray-500 px-4 py-2 rounded hover:bg-gray-500 hover:text-white transition">
                        Kembali
                    </a>
                    <a href="{{ route('students.edit', $student->id) }}" 
                       class="border border-yellow-500 text-yellow-500 px-4 py-2 rounded hover:bg-yellow-500 hover:text-white transition">
                        Edit
                    </a>
                </div>

                @if(session('error'))
                    <p class="text-red-500 mt-4">{{ session('error') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
