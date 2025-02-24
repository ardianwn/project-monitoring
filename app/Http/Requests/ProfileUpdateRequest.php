<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'nim' => [
                'nullable', 'string', 'max:255',
                Rule::unique('students', 'nim')->ignore(optional($this->user()->student)->id),
            ],
            'class_id' => ['nullable', 'exists:classes,id'],
        ];
    }
}
