<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Student;
use App\Models\ClassModel;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $classes = ClassModel::all();
        return view('profile.edit', [
            'user' => $request->user(),
            'classes' => $classes, // Kirim data kelas ke view
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    // Debugging: Log semua data yang diterima
    Log::info('Data yang diterima dari form:', $request->all());

    // Update data user (nama & email)
    $user->fill($request->validated());

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    // Jika user adalah mahasiswa, update atau buat entri di tabel students
    if ($user->isMahasiswa()) {
        $student = Student::updateOrCreate(
            ['user_id' => $user->id],
            ['nim' => $request->nim, 'class_id' => $request->class_id]
        );

        // Debugging: Log data yang disimpan
        Log::info('Data yang disimpan ke tabel students:', $student->toArray());
    }

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}
}
