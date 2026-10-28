@props(['type' => 'em-andamento'])

@php
    // Normaliza o tipo para caixa baixa para evitar problemas de case sensitive
    $typeNormalized = mb_strtolower($type);

    $class = match($typeNormalized) {
        'concluido', 'concluída no prazo', 'ativo', 'sim', 'ativa' => 'badge-concluido',
        'em-andamento', 'em andamento', 'recurso' => 'badge-em-andamento',
        'nao-iniciado', 'não iniciado', 'gestor' => 'badge-nao-iniciado',
        'atrasado', 'concluída com atraso', 'inativo', 'nao', 'inativa' => 'badge-atrasado',
        default => 'badge-em-andamento'
    };
@endphp

<span {{ $attributes->merge(['class' => "badge {$class}"]) }}>
    {{ $slot }}
</span>
