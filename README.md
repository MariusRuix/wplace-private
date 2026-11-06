# PixelMap (uso personal)

Mapa del mundo donde pintas pixeles. Leaflet + OpenStreetMap + PHP + SQLite.

## Archivos
- `index.html` — mapa, paleta y dibujo de pixeles
- `api.php` — guarda/lee pixeles; crea sola `data/pixels.sqlite` (protegida con .htaccess)
- `deploy.ps1` / `deploy.sh` — suben todo a Hostinger por SSH

## Base de datos
`data/pixels.sqlite` no se sube al repositorio (esta en `.gitignore`). En el repo va una copia vacia:
`data/pixels.sqlite.example`. Para usarla, copiala o renombrala a `data/pixels.sqlite`.
Si no existe, `api.php` la crea sola la primera vez que se pinta.

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
- **Pixeles nuevos y confirmar:** todo lo que pintas (clic, trazo, bote, pintado automatico, deshacer) queda como *nuevo* solo en tu navegador, marcado con borde blanco (X roja = se borrara). Aparece una barra con el numero de pixeles nuevos:
  - **Confirmar** (o **Enter**): los guarda en el servidor.
  - **Descartar**: los quita sin guardar.
  - **Resaltar**: atenua lo guardado y la plantilla para ver solo lo nuevo.
  Si cierras la pagina con pixeles sin confirmar, el navegador avisa.
- **Clic:** pinta un pixel.
- **Mantener Espacio + mover el mouse:** pinta por donde pasa el cursor (suelta Espacio para parar).
- El cuadro con tachado de la paleta borra.
- **Cuentagotas** (primer boton de la paleta o tecla **I**): clic en el mapa para tomar el color de la plantilla en ese pixel (o del pixel pintado si no hay plantilla ahi). El color queda seleccionado y se agrega a la paleta con borde punteado; clic derecho sobre el lo quita. Esc cancela.
- **Bote de pintura** (tecla **B**): solo funciona dentro de la plantilla. Al pasar el mouse muestra una vista previa del area y cuantos pixeles se pintaran. Dos modos (se eligen en el panel Plantilla):
  - **Area cerrada (lo pintado):** como el bote de Paint; rellena los pixeles conectados con el mismo color pintado (o vacios) hasta chocar con otro color. Sirve para dibujar un contorno y rellenarlo.
  - **Color de la plantilla:** rellena el bloque continuo del mismo color de la imagen.
  Usa el color de la paleta, o los de la plantilla si esta marcado "Pintar con los colores de la plantilla"; con el borrador, borra. Esc cancela.
- **Deshacer** (boton con flecha o **Ctrl+Z**): revierte el ultimo clic, trazo con Espacio, relleno o pintado automatico (hasta 50 pasos). Si lo deshecho ya estaba confirmado, el cambio queda como nuevo y hay que confirmarlo.

## Plantilla (imagen guia)
- Panel **Plantilla** (arriba a la derecha): elige una imagen; aparece centrada en la vista.
- **Ancho:** cuantos pixeles del mapa ocupa (1 px de imagen = 1 pixel del mapa). Por defecto max. 200.
- **Mover:** luego haz clic en el mapa donde ira la esquina superior izquierda (Esc cancela).
- **Opacidad / Ocultar / Quitar.** La tecla **Alt** (sola) muestra/oculta la plantilla.
- **Pintar con los colores de la plantilla:** el clic y el trazo con Espacio usan el color de la imagen en ese pixel; fuera de la imagen o en zonas transparentes no pinta. Elegir un color de la paleta lo desactiva.
- **Ver faltantes:** muestra solo lo que falta de la plantilla (con un punto en cada pixel); lo ya pintado correctamente se ve solido. Los pixeles pintados con otro color cuentan como faltantes. Debajo aparece el progreso (pintados / total y %).
- **Ir a faltante:** centra el mapa en el siguiente pixel que falta (cada clic avanza al siguiente).
- **Pintar todo automatico:** agrega como nuevos todos los pixeles que faltan (o estan mal) con los colores de la plantilla. Se guardan al pulsar Confirmar (se envian en lotes de 500).
- La plantilla se recuerda en ese navegador (no se sube al servidor).

## Opciones
- **Contrasena para pintar:** en `api.php`, `const PASSWORD = 'algo';`. El navegador la pide una vez.
- **Tamano del pixel:** `const Z` en `index.html` (13 = ~19 m; 14 = ~10 m). Cambiarlo despues desalinea los pixeles ya pintados.
- **Respaldo:** descarga `public_html/data/pixels.sqlite`.
