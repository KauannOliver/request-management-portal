<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero' => 'required|string|max:50',
            'tipo' => 'required|in:SAU,SD',
            'nome_solicitante' => 'required|string|max:255',
            'descricao' => 'required|string|max:1000',
            'observacoes' => 'nullable|string|max:2000',
            'etapa_id' => 'required|exists:etapas,id',
            'percentual_conclusao' => 'required|integer|min:0|max:100',
            'data_abertura' => 'required|date',
            'data_prazo' => 'required_if:tipo,SD|nullable|date',
            'recursos' => 'required|array|min:1',
            'recursos.*' => 'exists:usuarios,id',
        ];
    }
}
