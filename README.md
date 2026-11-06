# PixelMap (uso personal)

Mapa del mundo donde pintas pixeles. Leaflet + OpenStreetMap + PHP + SQLite.

## Archivos
- `index.html` — mapa, paleta y dibujo de pixeles
- `api.php` — guarda/lee pixeles; crea sola `data/pixels.sqlite` (protegida con .htaccess)
- `deploy.ps1` / `deploy.sh` — suben todo a Hostinger por SSH

## Probar en tu PC
```
php -S localhost:8000
```
Abre http://localhost:8000

## Desplegar en Hostinger
1. hPanel > Avanzado > **Acceso SSH**: activalo y copia usuario, IP y puerto (65002).
2. Edita las variables al inicio de `deploy.ps1` (Windows) o `deploy.sh`.
3. Ejecuta en la carpeta del proyecto:
   - Windows: `.\deploy.ps1`
   - Mac/Linux: `./deploy.sh`

Comando directo equivalente:
```
scp -P 65002 index.html api.php u123456789@IP:domains/tudominio.com/public_html/
```
Sin SSH: sube `index.html` y `api.php` a `public_html` con el Administrador de archivos.

## Uso
- **Clic:** pinta un pixel.
- **Mantener Espacio + mover el mouse:** pinta por donde pasa el cursor (suelta Espacio para parar).
- El cuadro con tachado de la paleta borra.

## Opciones
- **Contrasena para pintar:** en `api.php`, `const PASSWORD = 'algo';`. El navegador la pide una vez.
- **Tamano del pixel:** `const Z` en `index.html` (13 = ~19 m; 14 = ~10 m). Cambiarlo despues desalinea los pixeles ya pintados.
- **Respaldo:** descarga `public_html/data/pixels.sqlite`.
