<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Data Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-400 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Daftar Mahasiswa</h3>

                <!-- Form Pencarian & Filter -->
                <form method="GET" action="{{ route('students.index') }}" class="mb-6 flex space-x-4">
                    <!-- Input Pencarian -->
                    <input type="text" name="search" placeholder="Cari Nama, Email, atau NIM..."
                           value="{{ request('search') }}"
                           class="px-4 py-2 border rounded-md w-1/2 text-gray-900 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring focus:ring-blue-300">

                    <!-- Dropdown Filter Kelas -->
                    <select name="class_id" class="px-4 py-2 border rounded-md w-1/4 text-gray-900 dark:text-gray-300 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring focus:ring-blue-300">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Tombol Cari -->
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Cari
                    </button>
                </form>

                <!-- Tabel Mahasiswa -->
                <table class="min-w-full bg-white dark:bg-gray-800 border text-gray-400 border-gray-200 dark:border-gray-200">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 px-4 border-r">Nama</th>
                            <th class="py-2 px-4 border-r">Email</th>
                            <th class="py-2 px-4 border-r">NIM</th>
                            <th class="py-2 px-4 border-r">GitHub Username</th>
                            <th class="py-2 px-4 border-r">Kelas</th>
                            <th class="py-2 px-4 border-r">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr class="border-b">
                                <td class="py-2 px-4 border-r">{{ optional($student->user)->name }}</td>
                                <td class="py-2 px-4 border-r">{{ optional($student->user)->email }}</td>
                                <td class="py-2 px-4 border-r">{{ $student->nim ?? 'Belum Ada NIM' }}</td>
                                <td class="py-2 px-4 border-r">
                                    {{ $student->github_username ?? optional($student->user)->github_username ?? 'Tidak Ada Username' }}
                                </td>
                                <td class="py-2 px-4 border-r">
                                    {{ optional($student->class)->name ?? 'Belum Ada Kelas' }}
                                </td>
                                <td class="py-2 px-4 border-r flex space-x-2">
                                    <!-- Tombol Detail -->
                                    <a href="{{ route('students.show', $student->id) }}" 
                                       class="border border-blue-500 text-blue-500 px-3 py-1 rounded hover:bg-blue-500 hover:text-white transition">
                                       Detail
                                    </a>

                                    <!-- Tombol Edit -->
                                    <a href="{{ route('students.edit', $student->id) }}" 
                                       class="border border-yellow-500 text-yellow-500 px-3 py-1 rounded hover:bg-yellow-500 hover:text-white transition">
                                       Edit
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="border border-red-500 text-red-500 px-3 py-1 rounded hover:bg-red-500 hover:text-white transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pesan Sukses -->
                @if(session('success'))
                    <p class="text-green-500 mt-4">{{ session('success') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
