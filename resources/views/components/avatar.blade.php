@props(['name'])

@php
    // Divide o nome, extrai as primeiras letras dos dois primeiros nomes e une-as em maiúsculo
    $nomes = array_filter(explode(' ', $name));
    $iniciais = '';
    if (count($nomes) > 0) {
        $iniciais .= mb_substr(array_shift($nomes), 0, 1);
    }
    if (count($nomes) > 0) {
        $iniciais .= mb_substr(array_shift($nomes), 0, 1);
    }
    $iniciais = mb_strtoupper($iniciais);
@endphp

<div {{ $attributes->merge(['class' => 'user-avatar-initials']) }} title="{{ $name }}">
    {{ $iniciais }}
</div>
