#!/usr/bin/env bash
# Despliegue a Hostinger (Mac/Linux/Git Bash)
# Uso:  ./deploy.sh
set -euo pipefail

USER_SSH="u123456789"            # tu usuario SSH
HOST_SSH="123.45.67.89"          # IP del servidor
PORT=65002                       # puerto SSH de Hostinger
DOMAIN="tudominio.com"
REMOTE="domains/$DOMAIN/public_html"   # agrega /pixelmap si lo quieres en subcarpeta

cd "$(dirname "$0")"
ssh -p "$PORT" "$USER_SSH@$HOST_SSH" "mkdir -p $REMOTE"
scp -P "$PORT" index.html api.php "$USER_SSH@$HOST_SSH:$REMOTE/"
echo "Listo: https://$DOMAIN/"
