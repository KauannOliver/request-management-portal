<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'nome' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:usuarios,email,{$id}",
            'perfil' => 'required|in:gestor,recurso',
            'ativo' => 'required|boolean',
        ];
    }
}
