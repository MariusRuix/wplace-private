<?php
// PixelMap - API minima (PHP + SQLite)
// GET  api.php?x1=&y1=&x2=&y2=   -> [[x,y,"#rrggbb"], ...]
// POST api.php  {"x":1,"y":2,"color":"#ff0000"}  (color null = borrar)
// POST api.php  {"pixels":[[x,y,"#rrggbb"], ...]}  (lote, para el pintado automatico)

// Contrasena opcional para pintar. Dejala vacia para no pedir nada.
const PASSWORD = '';

header('Content-Type: application/json');
header('Cache-Control: no-store');

function fail($code, $msg) {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

// Carpeta de datos protegida (se crea sola la primera vez)
$dir = __DIR__ . '/data';
if (!is_dir($dir) && !mkdir($dir, 0755, true)) fail(500, 'No se pudo crear la carpeta data/');
if (!file_exists("$dir/.htaccess")) file_put_contents("$dir/.htaccess", "Require all denied\nDeny from all\n");
if (!file_exists("$dir/index.html")) file_put_contents("$dir/index.html", '');

try {
    $db = new PDO("sqlite:$dir/pixels.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('CREATE TABLE IF NOT EXISTS pixels (
        x INTEGER NOT NULL,
        y INTEGER NOT NULL,
        color TEXT NOT NULL,
        updated_at INTEGER NOT NULL,
        PRIMARY KEY (x, y)
    ) WITHOUT ROWID');
} catch (Exception $e) {
    fail(500, 'Error de base de datos');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    foreach (['x1', 'y1', 'x2', 'y2'] as $k) {
        if (!isset($_GET[$k]) || !is_numeric($_GET[$k])) fail(400, "Falta $k");
    }
    $x1 = (int)$_GET['x1']; $y1 = (int)$_GET['y1'];
    $x2 = (int)$_GET['x2']; $y2 = (int)$_GET['y2'];
    $st = $db->prepare('SELECT x, y, color FROM pixels
        WHERE x BETWEEN ? AND ? AND y BETWEEN ? AND ? LIMIT 200000');
    $st->execute([min($x1, $x2), max($x1, $x2), min($y1, $y2), max($y1, $y2)]);
    $out = [];
    while ($r = $st->fetch(PDO::FETCH_NUM)) $out[] = [(int)$r[0], (int)$r[1], $r[2]];
    echo json_encode($out);
    exit;
}

if ($method === 'POST') {
    if (PASSWORD !== '' && ($_SERVER['HTTP_X_KEY'] ?? '') !== PASSWORD) fail(401, 'Contrasena incorrecta');

    $in = json_decode(file_get_contents('php://input'), true);

    // Lote: {"pixels": [[x, y, "#rrggbb"], ...]} (maximo 5000 por peticion)
    if (is_array($in) && isset($in['pixels']) && is_array($in['pixels'])) {
        if (count($in['pixels']) > 5000) fail(400, 'Maximo 5000 pixeles por lote');
        $st = $db->prepare('INSERT OR REPLACE INTO pixels (x, y, color, updated_at) VALUES (?, ?, ?, ?)');
        $del = $db->prepare('DELETE FROM pixels WHERE x = ? AND y = ?');
        $now = time();
        $db->beginTransaction();
        foreach ($in['pixels'] as $p) {
            // color null = borrar ese pixel
            if (!is_array($p) || count($p) !== 3 || !is_int($p[0]) || !is_int($p[1])
                || ($p[2] !== null && (!is_string($p[2]) || !preg_match('/^#[0-9a-fA-F]{6}$/', $p[2])))) {
                $db->rollBack();
                fail(400, 'Pixel invalido en el lote');
            }
            if ($p[2] === null) $del->execute([$p[0], $p[1]]);
            else $st->execute([$p[0], $p[1], strtolower($p[2]), $now]);
        }
        $db->commit();
        echo json_encode(['ok' => true, 'count' => count($in['pixels'])]);
        exit;
    }

    if (!is_array($in) || !isset($in['x'], $in['y']) || !is_int($in['x']) || !is_int($in['y'])) {
        fail(400, 'Datos invalidos');
    }
    $color = $in['color'] ?? null;
    if ($color === null) {
        $db->prepare('DELETE FROM pixels WHERE x = ? AND y = ?')->execute([$in['x'], $in['y']]);
    } else {
        if (!is_string($color) || !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) fail(400, 'Color invalido');
        $db->prepare('INSERT OR REPLACE INTO pixels (x, y, color, updated_at) VALUES (?, ?, ?, ?)')
           ->execute([$in['x'], $in['y'], strtolower($color), time()]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

fail(405, 'Metodo no permitido');
