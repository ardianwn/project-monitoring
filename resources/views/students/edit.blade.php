<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Edit Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-400 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Edit Informasi Mahasiswa</h3>

                <form method="POST" action="{{ route('students.update', $student->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="user_id" :value="__('Nama Mahasiswa')" />
                        <select id="user_id" name="user_id" class="block w-full mt-1 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-400 rounded-md shadow-sm">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $student->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="nim" :value="__('NIM')" />
                        <x-text-input id="nim" name="nim" type="text" class="mt-1 block w-full" 
                            :value="old('nim', $student->nim)" autocomplete="nim" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nim')" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="github_username" :value="__('GitHub Username')" />
                        <x-text-input id="github_username" name="github_username" type="text" class="mt-1 block w-full" 
                            :value="old('github_username', $student->github_username)" autocomplete="github_username" required />
                        <x-input-error class="mt-2" :messages="$errors->get('github_username')" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="class_id" :value="__('Kelas')" />
                        <select id="class_id" name="class_id" class="block w-full mt-1 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-400 rounded-md shadow-sm">
                            <option value="">Pilih Kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ $student->class_id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('class_id')" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                        <a href="{{ route('students.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
