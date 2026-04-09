<?php
// ══════════════════════════════════════════════════
//  MAYA · McCain API — Endpoints para n8n y dashboard
// ══════════════════════════════════════════════════

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/mccain_config.php';

// ── Autenticación ────────────────────────────────
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
if ($apiKey !== MCCAIN_API_KEY) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

// ── Helpers ──────────────────────────────────────
function jsonOut(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function normalizeNumber(string $n): string {
    return ltrim(trim($n), '+');
}

// ── Router ────────────────────────────────────────
switch ($action) {

    // ─────────────────────────────────────────────
    // GET ?action=vendedor&whatsapp=+573106512999
    // Busca vendedor por número de WhatsApp
    // ─────────────────────────────────────────────
    case 'vendedor': {
        $wp = $_GET['whatsapp'] ?? '';
        if (!$wp) {
            jsonOut(['ok' => false, 'error' => 'whatsapp param required'], 400);
        }
        $wpNorm = normalizeNumber($wp);

        foreach ($MCCAIN_VENDEDORES as $v) {
            if (normalizeNumber($v['numero_whatsapp']) === $wpNorm) {
                jsonOut(['ok' => true, 'vendedor' => $v]);
            }
        }
        // Retorna 200 con ok:false para que el IF node en n8n lo maneje
        jsonOut(['ok' => false, 'registrado' => false, 'whatsapp' => $wp]);
    }

    // ─────────────────────────────────────────────
    // POST ?action=registrar_vendedor
    // Body: {nombre, numero_whatsapp, region?}
    // ─────────────────────────────────────────────
    case 'registrar_vendedor': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['ok' => false, 'error' => 'POST required'], 405);
        }
        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $nombre  = trim($body['nombre'] ?? $_POST['nombre'] ?? '');
        $numero  = trim($body['numero_whatsapp'] ?? $_POST['numero_whatsapp'] ?? '');
        $region  = trim($body['region'] ?? $_POST['region'] ?? '');

        if (!$nombre || !$numero) {
            jsonOut(['ok' => false, 'error' => 'nombre y numero_whatsapp son requeridos'], 400);
        }

        $numNorm = normalizeNumber($numero);

        // Verificar duplicado
        foreach ($MCCAIN_VENDEDORES as $v) {
            if (normalizeNumber($v['numero_whatsapp']) === $numNorm) {
                jsonOut(['ok' => false, 'error' => 'Este número ya está registrado', 'vendedor' => $v], 409);
            }
        }

        $newId    = max(array_keys($MCCAIN_VENDEDORES)) + 1;
        $now      = date('Y-m-d H:i:s');
        $vendedor = [
            'id'              => $newId,
            'nombre'          => $nombre,
            'numero_whatsapp' => '+' . $numNorm,
            'region'          => $region ?: 'Sin especificar',
            'estado'          => 'activo',
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        jsonOut(['ok' => true, 'vendedor' => $vendedor]);
    }

    // ─────────────────────────────────────────────
    // POST ?action=log_consulta
    // Body: {vendedor_id?, numero_whatsapp, tipo_consulta, mensaje_usuario, respuesta_bot?}
    // ─────────────────────────────────────────────
    case 'log_consulta': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['ok' => false, 'error' => 'POST required'], 405);
        }
        $body            = json_decode(file_get_contents('php://input'), true) ?? [];
        $vendedor_id     = $body['vendedor_id']    ?? $_POST['vendedor_id']    ?? null;
        $numero          = trim($body['numero_whatsapp']  ?? $_POST['numero_whatsapp']  ?? '');
        $tipo            = trim($body['tipo_consulta']    ?? $_POST['tipo_consulta']    ?? 'otro');
        $mensaje         = trim($body['mensaje_usuario']  ?? $_POST['mensaje_usuario']  ?? '');
        $respuesta       = trim($body['respuesta_bot']    ?? $_POST['respuesta_bot']    ?? '');

        if (!$numero || !$mensaje) {
            jsonOut(['ok' => false, 'error' => 'numero_whatsapp y mensaje_usuario requeridos'], 400);
        }

        $newId = count($MCCAIN_CONSULTAS) + 1;

        jsonOut(['ok' => true, 'id' => $newId]);
    }

    // ─────────────────────────────────────────────
    // GET ?action=stats
    // Estadísticas globales para el dashboard
    // ─────────────────────────────────────────────
    case 'stats': {
        $hoy   = date('Y-m-d');
        $week  = date('Y-m-d', strtotime('-7 days'));

        $totalConsultas    = count($MCCAIN_CONSULTAS);
        $totalVendedores   = count($MCCAIN_VENDEDORES);
        $vendedoresActivos = count(array_filter($MCCAIN_VENDEDORES, fn($v) => $v['estado'] === 'activo'));
        $consultasHoy      = count(array_filter($MCCAIN_CONSULTAS, fn($c) => substr($c['created_at'], 0, 10) === $hoy));
        $consultasSemana   = count(array_filter($MCCAIN_CONSULTAS, fn($c) => substr($c['created_at'], 0, 10) >= $week));

        $porTipo = [];
        foreach ($MCCAIN_CONSULTAS as $c) {
            $t = $c['tipo_consulta'];
            $porTipo[$t] = ($porTipo[$t] ?? 0) + 1;
        }
        arsort($porTipo);
        $porTipoArr = array_map(fn($k, $v) => ['tipo' => $k, 'total' => $v], array_keys($porTipo), $porTipo);

        $porRegion = [];
        foreach ($MCCAIN_VENDEDORES as $v) {
            $r = $v['region'];
            $porRegion[$r] = ($porRegion[$r] ?? 0) + 1;
        }

        $porDia = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $porDia[$d] = 0;
        }
        foreach ($MCCAIN_CONSULTAS as $c) {
            $d = substr($c['created_at'], 0, 10);
            if (isset($porDia[$d])) $porDia[$d]++;
        }

        jsonOut([
            'ok'                => true,
            'total_consultas'   => $totalConsultas,
            'total_vendedores'  => $totalVendedores,
            'vendedores_activos'=> $vendedoresActivos,
            'consultas_hoy'     => $consultasHoy,
            'consultas_semana'  => $consultasSemana,
            'por_tipo'          => $porTipoArr,
            'por_region'        => $porRegion,
            'por_dia'           => $porDia,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET ?action=vendedores&region=...&estado=...
    // Lista de vendedores con filtros opcionales
    // ─────────────────────────────────────────────
    case 'vendedores': {
        $region = $_GET['region'] ?? '';
        $estado = $_GET['estado'] ?? '';

        $result = array_values(array_filter($MCCAIN_VENDEDORES, function($v) use ($region, $estado) {
            if ($region && $v['region'] !== $region) return false;
            if ($estado && $v['estado'] !== $estado) return false;
            return true;
        }));

        jsonOut(['ok' => true, 'vendedores' => $result, 'total' => count($result)]);
    }

    default:
        jsonOut(['ok' => false, 'error' => "Acción '{$action}' no reconocida"], 400);
}
