#!/bin/bash

# Configurações do Restore SQLite
APP_DIR="/home/kauantavares/apps/gestao-sau-sd"
DB_FILE="${APP_DIR}/data/database.sqlite"
BACKUP_FILE=$1

if [ -z "$BACKUP_FILE" ]; then
    echo "Uso: ./restore.sh <caminho_do_arquivo_backup_de_origem.sqlite>"
    exit 1
fi

if [ ! -f "$BACKUP_FILE" ]; then
    echo "ERRO: O arquivo de backup informado não existe: $BACKUP_FILE"
    exit 1
fi

echo "=== RESTAURAÇÃO DE BANCO DE DADOS SQLITE ==="
echo "Origem: $BACKUP_FILE"
echo "Destino: $DB_FILE"
echo ""
echo "ATENÇÃO: O banco de dados ativo será substituído! Todos os dados atuais não salvos serão perdidos."
echo "Deseja realmente continuar com a restauração? (digite 'sim' para confirmar):"
read -r CONFIRMATION

if [ "$CONFIRMATION" != "sim" ]; then
    echo "Operação abortada pelo operador."
    exit 0
fi

# 1. Parar temporariamente os containers Docker para liberar travas de gravação do SQLite
echo "Parando os containers Docker..."
cd "$APP_DIR" || exit 1
docker compose down

# 2. Criar uma cópia de segurança emergencial (fallback) do banco atual
if [ -f "$DB_FILE" ]; then
    echo "Fazendo cópia de segurança do banco ativo atual (database.sqlite.bak)..."
    cp "$DB_FILE" "${DB_FILE}.bak"
fi

# 3. Substituir o arquivo do banco ativo pelo backup selecionado
echo "Substituindo banco SQLite pelo backup..."
cp "$BACKUP_FILE" "$DB_FILE"

# 4. Ajustar as permissões de gravação para o UID do www-data do container (1000)
echo "Ajustando permissões do arquivo SQLite..."
chown 1000:1000 "$DB_FILE"
chmod 664 "$DB_FILE"

# 5. Reiniciar a stack Docker Compose
echo "Reiniciando containers..."
docker compose up -d

echo "=== PROCESSO DE RESTAURAÇÃO CONCLUÍDO COM SUCESSO ==="
