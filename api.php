<?php
// ══════════════════════════════════════════════════
//  MAX · Portal RRHH — API JSON (demo)
//  Endpoints para integración con n8n / agente MAX
// ══════════════════════════════════════════════════

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); exit;
}

require_once __DIR__ . '/config.php';

// Simple API key check for demo
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
if ($apiKey !== 'max-demo-2026') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Use header X-Api-Key: max-demo-2026']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

    // ── GET /api.php?action=empleado&whatsapp=+549... ──
    case 'empleado':
        $wp = $_GET['whatsapp'] ?? '';
        $found = null;
        foreach ($EMPLEADOS as $e) {
            if ($e['whatsapp'] === $wp) { $found = $e; break; }
        }
        if (!$found) {
            http_response_code(404);
            echo json_encode(['error' => 'Empleado no encontrado', 'whatsapp' => $wp]);
            exit;
        }
        // Quitar datos sensibles para el bot
        unset($found['sueldo']);
        $found['dias_vacaciones_libres'] = $found['dias_vacaciones_anuales'] - $found['dias_vacaciones_usados'];
        $found['dias_franco_libres']     = $found['dias_franco_disponibles'] - $found['dias_franco_usados'];
        echo json_encode(['ok' => true, 'empleado' => $found]);
        break;

    // ── GET /api.php?action=dias&empleado_id=1 ──
    case 'dias':
        $eid = (int)($_GET['empleado_id'] ?? 0);
        $emp = $EMPLEADOS[$eid] ?? null;
        if (!$emp) {
            http_response_code(404);
            echo json_encode(['error' => 'Empleado no encontrado']);
            exit;
        }
        echo json_encode([
            'ok'                      => true,
            'empleado_id'             => $eid,
            'nombre'                  => $emp['nombre'],
            'vacaciones_anuales'      => $emp['dias_vacaciones_anuales'],
            'vacaciones_usadas'       => $emp['dias_vacaciones_usados'],
            'vacaciones_disponibles'  => $emp['dias_vacaciones_anuales'] - $emp['dias_vacaciones_usados'],
            'francos_disponibles'     => $emp['dias_franco_disponibles'] - $emp['dias_franco_usados'],
            'francos_usados'          => $emp['dias_franco_usados'],
        ]);
        break;

    // ── GET /api.php?action=solicitudes&empleado_id=1 ──
    case 'solicitudes':
        $eid    = isset($_GET['empleado_id']) ? (int)$_GET['empleado_id'] : null;
        $estado = $_GET['estado'] ?? '';
        $sols   = $SOLICITUDES;
        if ($eid) $sols = array_filter($sols, fn($s) => $s['empleado_id'] === $eid);
        if ($estado) $sols = array_filter($sols, fn($s) => $s['estado'] === $estado);
        usort($sols, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
        $out = [];
        foreach ($sols as $s) {
            $out[] = [
                'id'           => $s['id'],
                'codigo'       => $s['codigo'],
                'tipo'         => $s['tipo'],
                'tipo_label'   => $TIPOS_SOLICITUD[$s['tipo']]['label'] ?? $s['tipo'],
                'empleado'     => $EMPLEADOS[$s['empleado_id']]['nombre'] ?? null,
                'estado'       => $s['estado'],
                'estado_label' => $ESTADOS[$s['estado']]['label'] ?? $s['estado'],
                'fecha_inicio' => $s['fecha_inicio'],
                'fecha_fin'    => $s['fecha_fin'],
                'dias'         => $s['dias'],
                'motivo'       => $s['motivo'],
                'created_at'   => $s['created_at'],
            ];
        }
        echo json_encode(['ok' => true, 'total' => count($out), 'solicitudes' => array_values($out)]);
        break;

    // ── POST /api.php?action=crear_solicitud ──
    case 'crear_solicitud':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']); exit;
        }
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $eid   = (int)($body['empleado_id'] ?? 0);
        $tipo  = $body['tipo'] ?? '';
        $fi    = $body['fecha_inicio'] ?? date('Y-m-d');
        $ff    = $body['fecha_fin']    ?? $fi;
        $motivo= $body['motivo'] ?? '';

        if (!isset($EMPLEADOS[$eid])) {
            http_response_code(422);
            echo json_encode(['error' => 'Empleado no encontrado']); exit;
        }
        if (!isset($TIPOS_SOLICITUD[$tipo])) {
            http_response_code(422);
            echo json_encode(['error' => 'Tipo de solicitud inválido', 'tipos_validos' => array_keys($TIPOS_SOLICITUD)]); exit;
        }

        // Calcular días hábiles (simplificado)
        $d1   = new DateTime($fi);
        $d2   = new DateTime($ff);
        $diff = max(0, (int)$d1->diff($d2)->days + 1);

        $newId  = max(array_keys($SOLICITUDES)) + 1;
        $prefix = strtoupper(substr($tipo, 0, 3));
        $codigo = $prefix.'-'.date('Y').'-'.str_pad($newId, 3, '0', STR_PAD_LEFT);

        echo json_encode([
            'ok'          => true,
            'mensaje'     => 'Solicitud registrada (demo). En producción se guarda en la BD.',
            'solicitud'   => [
                'id'           => $newId,
                'codigo'       => $codigo,
                'tipo'         => $tipo,
                'empleado_id'  => $eid,
                'estado'       => $TIPOS_SOLICITUD[$tipo]['requiere_lider'] ? 'pendiente_lider' : 'pendiente_rrhh',
                'fecha_inicio' => $fi,
                'fecha_fin'    => $ff,
                'dias'         => $diff,
                'motivo'       => $motivo,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ]);
        break;

    // ── GET /api.php?action=stats ──
    case 'stats':
        $pendientes = count(array_filter($SOLICITUDES, fn($s) => in_array($s['estado'], ['pendiente_lider','pendiente_rrhh'])));
        $aprobadas  = count(array_filter($SOLICITUDES, fn($s) => $s['estado'] === 'aprobada'));
        $rechazadas = count(array_filter($SOLICITUDES, fn($s) => $s['estado'] === 'rechazada'));
        echo json_encode([
            'ok'         => true,
            'total'      => count($SOLICITUDES),
            'pendientes' => $pendientes,
            'aprobadas'  => $aprobadas,
            'rechazadas' => $rechazadas,
            'empleados'  => count($EMPLEADOS),
        ]);
        break;

    // ── POST /api.php?action=log_consulta ──
    case 'log_consulta':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']); exit;
        }
        $body           = json_decode(file_get_contents('php://input'), true) ?? [];
        $empleado_id    = isset($body['empleado_id'])     ? (int)$body['empleado_id']    : null;
        $numero         = trim($body['numero_whatsapp']   ?? '');
        $tipo           = trim($body['tipo_consulta']     ?? 'otro');
        $msg_usuario    = trim($body['mensaje_usuario']   ?? '');
        $resp_bot       = trim($body['respuesta_bot']     ?? '');
        $solicitud_id   = isset($body['solicitud_id'])    ? (int)$body['solicitud_id']   : null;

        if (!$numero || !$msg_usuario) {
            http_response_code(422);
            echo json_encode(['error' => 'numero_whatsapp y mensaje_usuario son requeridos']); exit;
        }

        // En demo: simular insert y retornar nuevo ID
        global $LOG_CONSULTAS;
        $newLogId = count($LOG_CONSULTAS) + 1;
        // En producción: INSERT INTO log_consultas (empleado_id, numero_whatsapp, tipo_consulta, mensaje_usuario, respuesta_bot, solicitud_id) VALUES (...)
        echo json_encode([
            'ok'      => true,
            'id'      => $newLogId,
            'mensaje' => 'Consulta registrada (demo). En producción se guarda en log_consultas.',
        ]);
        break;

    // ── GET /api.php?action=stats_consultas ──
    case 'stats_consultas':
        global $LOG_CONSULTAS;

        // Calcular stats desde el mock
        $hoy    = date('Y-m-d');
        $semana = date('Y-m-d', strtotime('-7 days'));

        $porTipo       = [];
        $totalHoy      = 0;
        $totalSemana   = 0;
        $numUnicos     = [];
        $porDia        = [];

        foreach ($LOG_CONSULTAS as $log) {
            $t   = $log['tipo_consulta'];
            $dia = substr($log['created_at'], 0, 10);

            if (!isset($porTipo[$t])) {
                $porTipo[$t] = ['tipo' => $t, 'total' => 0, 'usuarios_unicos' => [], 'generaron_solicitud' => 0];
            }
            $porTipo[$t]['total']++;
            $porTipo[$t]['usuarios_unicos'][$log['numero_whatsapp']] = true;
            if (!empty($log['solicitud_id'])) $porTipo[$t]['generaron_solicitud']++;

            if ($dia === $hoy)   $totalHoy++;
            if ($dia >= $semana) $totalSemana++;

            $numUnicos[$log['numero_whatsapp']] = true;
            $porDia[$dia] = ($porDia[$dia] ?? 0) + 1;
        }

        // Limpiar arrays internos
        foreach ($porTipo as &$pt) {
            $pt['usuarios_unicos'] = count($pt['usuarios_unicos']);
        }
        usort($porTipo, fn($a,$b) => $b['total'] - $a['total']);
        krsort($porDia);

        echo json_encode([
            'ok'              => true,
            'total_all'       => count($LOG_CONSULTAS),
            'total_hoy'       => $totalHoy,
            'total_semana'    => $totalSemana,
            'usuarios_unicos' => count($numUnicos),
            'por_tipo'        => array_values($porTipo),
            'por_dia'         => $porDia,
        ]);
        break;

    // ── Fallback ──
    default:
        http_response_code(400);
        echo json_encode([
            'error'    => 'Acción no reconocida',
            'acciones' => ['empleado','dias','solicitudes','crear_solicitud','stats','log_consulta','stats_consultas'],
            'auth'     => 'Enviar header X-Api-Key: max-demo-2026',
        ]);
}
