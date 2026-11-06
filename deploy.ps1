# Despliegue a Hostinger (Windows PowerShell)
# Uso:  .\deploy.ps1
# Datos en hPanel > Avanzado > Acceso SSH

$USER_SSH = "u123456789"            # tu usuario SSH
$HOST_SSH = "123.45.67.89"          # IP del servidor
$PORT     = 65002                   # puerto SSH de Hostinger
$DOMAIN   = "tudominio.com"
$REMOTE   = "domains/$DOMAIN/public_html"   # agrega /pixelmap si lo quieres en subcarpeta

ssh -p $PORT "$USER_SSH@$HOST_SSH" "mkdir -p $REMOTE"
scp -P $PORT index.html api.php "${USER_SSH}@${HOST_SSH}:$REMOTE/"
Write-Host "Listo: https://$DOMAIN/"
