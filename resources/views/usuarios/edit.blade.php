@extends('layouts.app')

@section('title', 'Editar Usuário ' . $usuario->nome . ' - Gestão SAU & SD')

@section('breadcrumbs')
    <a href="{{ route('usuarios.index') }}" class="breadcrumbs-item">Usuários</a>
    <span class="breadcrumbs-item">Editar</span>
@endsection

@section('content')
    <div class="page-header-row">
        <div class="page-title-group">
            <h2>Editar Usuário: {{ $usuario->nome }}</h2>
            <p>Atualize as permissões de perfil ou redefina a senha de acesso.</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            Voltar
        </a>
    </div>

    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; align-items: start; max-width: 1000px; margin: 0 auto;">
        
        <!-- CARD DE INFORMAÇÕES BÁSICAS -->
        <div class="card-section">
            <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.25rem;">
                Dados Cadastrais & Perfil
            </h3>

            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" class="form-confirm-leave">
                @csrf
                @method('PUT')

                <!-- Nome -->
                <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                    <label for="nome" class="form-label">Nome Completo <span style="color:var(--orange-alert);">*</span></label>
                    <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $usuario->nome) }}" required>
                    @error('nome') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- E-mail -->
                <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                    <label for="email" class="form-label">E-mail <span style="color:var(--orange-alert);">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required>
                    @error('email') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Perfil -->
                <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                    <label for="perfil" class="form-label">Perfil de Acesso <span style="color:var(--orange-alert);">*</span></label>
                    <select name="perfil" id="perfil" class="form-control @error('perfil') is-invalid @enderror" required>
                        <option value="gestor" {{ old('perfil', $usuario->perfil) === 'gestor' ? 'selected' : '' }}>Gestor (Consulta)</option>
                        <option value="recurso" {{ old('perfil', $usuario->perfil) === 'recurso' ? 'selected' : '' }}>Recurso (Escrita/Admin)</option>
                    </select>
                    @error('perfil') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Status Ativo -->
                <div class="form-group" style="min-width: 100%; margin-bottom: 1.5rem;">
                    <label for="ativo" class="form-label">Status do Acesso <span style="color:var(--orange-alert);">*</span></label>
                    <select name="ativo" id="ativo" class="form-control @error('ativo') is-invalid @enderror" required>
                        <option value="1" {{ old('ativo', $usuario->ativo) == 1 ? 'selected' : '' }}>Ativo (Permitido login)</option>
                        <option value="0" {{ old('ativo', $usuario->ativo) == 0 ? 'selected' : '' }}>Inativo (Bloqueado)</option>
                    </select>
                    @error('ativo') <span style="color:var(--orange-alert); font-size:0.8rem; display:block; margin-top:0.25rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Botões de Ação -->
                <div style="display: flex; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;" id="btn-submit-edit-usuario">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary" style="flex: 1;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- CARD DE REDEFINIÇÃO DE SENHA -->
        <div class="card-section">
            <h3 style="font-size: 1.15rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.25rem; color: var(--orange-alert);">
                Redefinir Senha de Acesso
            </h3>

            <form action="{{ route('usuarios.redefinir-senha', $usuario->id) }}" method="POST" class="form-confirm-leave-bypass">
                @csrf
                @method('PUT')

                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                    A senha antiga do usuário não será exibida. Digite a nova senha de segurança para redefinição imediata.
                </p>

                <!-- Nova Senha -->
                <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                    <label for="password" class="form-label">Nova Senha <span style="color:var(--orange-alert);">*</span></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres, com letras e números" required>
                    @error('password') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Confirmação da Nova Senha -->
                <div class="form-group" style="min-width: 100%; margin-bottom: 1.5rem;">
                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha <span style="color:var(--orange-alert);">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repita a nova senha exatamente" required>
                </div>

                <!-- Botão de Ação -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-danger" style="width: 100%;" id="btn-submit-redefinir-senha">
                        Confirmar Nova Senha
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
