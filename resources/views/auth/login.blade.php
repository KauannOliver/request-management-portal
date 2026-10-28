<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso - Gestão SAU & SD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card border-0 shadow-lg p-4 mx-3" style="max-width: 420px; width: 100%; border-radius: var(--radius-md);">
        <div class="text-center mb-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 32px; height: 32px; fill: var(--blue-primary);">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
                <span class="h4 fw-bold text-dark mb-0">Gestão SAU & SD</span>
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Insira suas credenciais de acesso corporativo</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show p-2 mb-3" role="alert" style="font-size: 0.85rem;">
                {{ session('error') }}
                <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="form-confirm-leave-bypass">
            @csrf

            <!-- E-mail -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">E-mail Corporativo</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="usuario@empresa.com" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Senha -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold text-secondary mb-0" style="font-size: 0.85rem;">Senha</label>
                    <button type="button" id="toggle-password" class="btn btn-link p-0 text-decoration-none fw-bold" style="font-size:0.8rem; color: var(--blue-primary);">Mostrar</button>
                </div>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <!-- Manter Conectado -->
            <div class="form-check mb-4">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" style="cursor: pointer;">
                <label for="remember" class="form-check-label text-secondary" style="font-size: 0.85rem; cursor: pointer; user-select: none;">Lembrar de mim</label>
            </div>

            <!-- Botão de Acesso -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="background-color: var(--blue-primary); border-color: var(--blue-primary);" id="btn-login-submit">
                Entrar no Sistema
            </button>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função simples para alternar visibilidade da senha
        document.getElementById('toggle-password').addEventListener('click', function () {
            const pwdInput = document.getElementById('password');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                this.textContent = 'Ocultar';
            } else {
                pwdInput.type = 'password';
                this.textContent = 'Mostrar';
            }
        });
    </script>
</body>
</html>
