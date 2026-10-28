@extends('layouts.app')

@section('title', 'Minha Conta - Gestão SAU & SD')

@section('breadcrumbs')
    <span class="breadcrumbs-item">Minha Conta</span>
@endsection

@section('content')
    <div class="page-header-row">
        <div class="page-title-group">
            <h2>Configurações da Minha Conta</h2>
            <p>Gerencie seus dados cadastrais e senha pessoal de acesso.</p>
        </div>
    </div>

    <div class="card-section" style="max-width: 650px; margin: 0 auto;">
        <form action="{{ route('minha-conta.update') }}" method="POST" class="form-confirm-leave">
            @csrf
            @method('PUT')

            <!-- Nome -->
            <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                <label for="nome" class="form-label">Seu Nome Completo <span style="color:var(--orange-alert);">*</span></label>
                <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $usuario->nome) }}" required>
                @error('nome') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- E-mail -->
            <div class="form-group" style="min-width: 100%; margin-bottom: 1.25rem;">
                <label for="email" class="form-label">Seu E-mail Corporativo <span style="color:var(--orange-alert);">*</span></label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required>
                @error('email') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <!-- Perfil de Acesso (Somente Leitura) -->
            <div class="form-group" style="min-width: 100%; margin-bottom: 1.5rem;">
                <label class="form-label">Perfil de Acesso</label>
                <input type="text" class="form-control" value="{{ strtoupper($usuario->perfil) }}" readonly style="background-color: var(--bg-main); color: var(--text-muted); cursor: not-allowed;">
                <span style="font-size:0.75rem; color:var(--text-muted);">Seu nível de acesso corporativo não pode ser alterado por você mesmo.</span>
            </div>

            <!-- Alerta sobre senha -->
            <div style="background-color: rgba(7, 154, 225, 0.05); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(7, 154, 225, 0.2); margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--navy); line-height: 1.4;">
                <strong>💡 Alteração de Senha:</strong> Caso queira manter sua senha atual de acesso, deixe os campos de senha abaixo vazios. Caso deseje alterar, digite a nova senha nos dois campos.
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <!-- Nova Senha -->
                <div class="form-group">
                    <label for="password" class="form-label">Nova Senha</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
                    @error('password') <span style="color:var(--orange-alert); font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Confirmação da Senha -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repita a senha">
                </div>
            </div>

            <!-- Botão de Envio -->
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 44px;" id="btn-submit-minha-conta">
                Salvar Configurações da Conta
            </button>
        </form>
    </div>
@endsection
