#!/bin/bash

# Configurações do Backup SQLite
APP_DIR="/home/kauantavares/apps/gestao-sau-sd"
BACKUP_DIR="${APP_DIR}/backups"
DB_FILE="${APP_DIR}/data/database.sqlite"
RETENCAO_DIAS=7
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/backup_${TIMESTAMP}.sqlite"
LOG_FILE="${BACKUP_DIR}/backup.log"

# Cria a pasta de backups se ela não existir
mkdir -p "$BACKUP_DIR"

echo "[$(date +"%Y-%m-%d %H:%M:%S")] Iniciando backup consistente online..." >> "$LOG_FILE"

# Verifica se o arquivo do banco existe no host
if [ ! -f "$DB_FILE" ]; then
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] ERRO: Banco de dados não encontrado em $DB_FILE." >> "$LOG_FILE"
    exit 1
fi

# Executa o comando de backup nativo e consistente do SQLite (evita cópia física instável)
sqlite3 "$DB_FILE" ".backup '$BACKUP_FILE'"

if [ $? -eq 0 ] && [ -f "$BACKUP_FILE" ]; then
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] SUCESSO: Backup criado em $BACKUP_FILE" >> "$LOG_FILE"
    
    # Aplica política de retenção de backups (remove arquivos modificados há mais de RETENCAO_DIAS)
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] Removendo backups mais antigos que $RETENCAO_DIAS dias..." >> "$LOG_FILE"
    find "$BACKUP_DIR" -name "backup_*.sqlite" -type f -mtime +$RETENCAO_DIAS -exec rm -f {} \;
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] Política de retenção aplicada com sucesso." >> "$LOG_FILE"
else
    echo "[$(date +"%Y-%m-%d %H:%M:%S")] ERRO: Falha crítica na geração do backup consistente." >> "$LOG_FILE"
    exit 1
fi
