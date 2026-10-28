<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email',
            'perfil' => 'required|in:gestor,recurso',
            'ativo' => 'required|boolean',
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ];
    }
}
