<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'login' => 'required|string|max:50|unique:users,login,' . $user->id,
            'password' => 'required|string|min:8|max:255',
            'email' => 'required|email|max:50|unique:users,email,' . $user->id,
            'last_name' => 'required|string|max:50',
            'first_name' => 'required|string|max:50',
        ];
    }
}
