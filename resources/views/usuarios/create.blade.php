@extends('layouts.app')

@section('title', 'Novo Usuário - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('usuarios.index') }}" class="breadcrumbs-item">Usuários</a>
    <span class="breadcrumbs-item">Cadastrar</span>
@endsection

@section('content')
    <div class="page-header-row">
        <div class="page-title-group">
            <h2>Cadastrar Usuário</h2>
            <p>Registre um novo colaborador no sistema.</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="card-section" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('usuarios.store') }}" method="POST" class="form-confirm-leave">
            @csrf

            <!-- Nome -->
            <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                <label for="nome" class="form-label">Nome Completo <span style="color:var(--orange-alert);">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" placeholder="Ex: Ana Souza" value="{{ old('nome') }}" required>
                @error('nome') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- E-mail -->
            <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                <label for="email" class="form-label">E-mail <span style="color:var(--orange-alert);">*</span></label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="ana@empresa.com" value="{{ old('email') }}" required>
                @error('email') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <!-- Perfil -->
                <div class="form-group">
                    <label for="perfil" class="form-label">Perfil de Acesso <span style="color:var(--orange-alert);">*</span></label>
                    <select name="perfil" id="perfil" class="form-control @error('perfil') is-invalid @enderror" required>
                        <option value="gestor" {{ old('perfil') === 'gestor' ? 'selected' : '' }}>Gestor (Consulta)</option>
                        <option value="recurso" {{ old('perfil') === 'recurso' ? 'selected' : '' }}>Recurso (Escrita/Admin)</option>
                    </select>
                    @error('perfil') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Status Ativo -->
                <div class="form-group">
                    <label for="ativo" class="form-label">Status <span style="color:var(--orange-alert);">*</span></label>
                    <select name="ativo" id="ativo" class="form-control @error('ativo') is-invalid @enderror" required>
                        <option value="1" {{ old('ativo', '1') === '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('ativo') === '0' ? 'selected' : '' }}>Inativo (Acesso bloqueado)</option>
                    </select>
                    @error('ativo') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Senha -->
            <div class="form-group" style="min-width: 100%; margin-bottom: 2rem;">
                <label for="password" class="form-label">Senha Inicial <span style="color:var(--orange-alert);">*</span></label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres, contendo letras e números" required>
                @error('password') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- Botões -->
            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;" id="btn-submit-criar-usuario">
                    Cadastrar Usuário
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary" style="flex: 1;">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
