<?php
// ══════════════════════════════════════════════════
//  MAX · Portal RRHH — Configuración y datos mock
//  LL&AA Llerena & Asociados Abogados
// ══════════════════════════════════════════════════

if (session_status() === PHP_SESSION_NONE) session_start();

// ── Usuarios del portal (contraseña: demo2026) ──────
define('PASS_HASH', password_hash('demo2026', PASSWORD_BCRYPT));

$PORTAL_USERS = [
    'rrhh' => [
        'hash'   => PASS_HASH,
        'nombre' => 'María Fernández',
        'rol'    => 'rrhh',
        'email'  => 'rrhh@llerena.com',
    ],
    'lider1' => [
        'hash'   => PASS_HASH,
        'nombre' => 'Carlos Ibáñez',
        'rol'    => 'lider',
        'email'  => 'ibañez@llerena.com',
        'area'   => 'Derecho Laboral',
    ],
    'lider2' => [
        'hash'   => PASS_HASH,
        'nombre' => 'Sofía Méndez',
        'rol'    => 'lider',
        'email'  => 'mendez@llerena.com',
        'area'   => 'Derecho Civil',
    ],
    'empleado1' => [
        'hash'        => PASS_HASH,
        'nombre'      => 'Laura García',
        'rol'         => 'empleado',
        'email'       => 'lgarcia@llerena.com',
        'empleado_id' => 1,
    ],
    'empleado2' => [
        'hash'        => PASS_HASH,
        'nombre'      => 'Tomás Ruiz',
        'rol'         => 'empleado',
        'email'       => 'truiz@llerena.com',
        'empleado_id' => 2,
    ],
    'finanzas' => [
        'hash'   => PASS_HASH,
        'nombre' => 'Roberto Sosa',
        'rol'    => 'finanzas',
        'email'  => 'rsosa@llerena.com',
    ],
];

// ── Empleados ────────────────────────────────────────
$EMPLEADOS = [
    1 => [
        'id'           => 1,
        'nombre'       => 'Laura García',
        'dni'          => '32.145.678',
        'puesto'       => 'Abogada Senior',
        'area'         => 'Derecho Laboral',
        'lider_id'     => 1,
        'fecha_ingreso'=> '2019-03-15',
        'sueldo'       => 850000,
        'email'        => 'lgarcia@llerena.com',
        'whatsapp'     => '+5491134567890',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 15,
        'dias_vacaciones_usados'  => 6,
        'dias_franco_disponibles' => 4,
        'dias_franco_usados'      => 2,
    ],
    2 => [
        'id'           => 2,
        'nombre'       => 'Tomás Ruiz',
        'dni'          => '35.890.234',
        'puesto'       => 'Abogado Junior',
        'area'         => 'Derecho Laboral',
        'lider_id'     => 1,
        'fecha_ingreso'=> '2022-08-01',
        'sueldo'       => 520000,
        'email'        => 'truiz@llerena.com',
        'whatsapp'     => '+5491145678901',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 10,
        'dias_vacaciones_usados'  => 3,
        'dias_franco_disponibles' => 2,
        'dias_franco_usados'      => 1,
    ],
    3 => [
        'id'           => 3,
        'nombre'       => 'Valentina López',
        'dni'          => '30.567.123',
        'puesto'       => 'Abogada Especialista',
        'area'         => 'Derecho Civil',
        'lider_id'     => 2,
        'fecha_ingreso'=> '2017-11-20',
        'sueldo'       => 980000,
        'email'        => 'vlopez@llerena.com',
        'whatsapp'     => '+5491156789012',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 21,
        'dias_vacaciones_usados'  => 14,
        'dias_franco_disponibles' => 5,
        'dias_franco_usados'      => 3,
    ],
    4 => [
        'id'           => 4,
        'nombre'       => 'Martín Herrera',
        'dni'          => '38.234.567',
        'puesto'       => 'Pasante Avanzado',
        'area'         => 'Derecho Civil',
        'lider_id'     => 2,
        'fecha_ingreso'=> '2024-02-12',
        'sueldo'       => 280000,
        'email'        => 'mherrera@llerena.com',
        'whatsapp'     => '+5491167890123',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 7,
        'dias_vacaciones_usados'  => 0,
        'dias_franco_disponibles' => 1,
        'dias_franco_usados'      => 0,
    ],
    5 => [
        'id'           => 5,
        'nombre'       => 'Camila Torres',
        'dni'          => '33.456.789',
        'puesto'       => 'Administrativa RRHH',
        'area'         => 'Administración',
        'lider_id'     => null,
        'fecha_ingreso'=> '2020-05-18',
        'sueldo'       => 450000,
        'email'        => 'ctorres@llerena.com',
        'whatsapp'     => '+5491178901234',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 15,
        'dias_vacaciones_usados'  => 9,
        'dias_franco_disponibles' => 3,
        'dias_franco_usados'      => 2,
    ],
    6 => [
        'id'           => 6,
        'nombre'       => 'Diego Morales',
        'dni'          => '29.345.678',
        'puesto'       => 'Abogado Senior',
        'area'         => 'Derecho Penal',
        'lider_id'     => null,
        'fecha_ingreso'=> '2016-07-04',
        'sueldo'       => 1100000,
        'email'        => 'dmorales@llerena.com',
        'whatsapp'     => '+5491189012345',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 21,
        'dias_vacaciones_usados'  => 7,
        'dias_franco_disponibles' => 6,
        'dias_franco_usados'      => 1,
    ],
    7 => [
        'id'           => 7,
        'nombre'       => 'Emerson Bezerra',
        'dni'          => '19063551',
        'puesto'       => 'Empleado',
        'area'         => 'General',
        'lider_id'     => 3,
        'fecha_ingreso'=> '2026-01-01',
        'sueldo'       => 0,
        'email'        => 'ebezerra@llerena.com',
        'whatsapp'     => '+5511968951221',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 15,
        'dias_vacaciones_usados'  => 0,
        'dias_franco_disponibles' => 3,
        'dias_franco_usados'      => 0,
    ],
    8 => [
        'id'           => 8,
        'nombre'       => 'Hector Azcurra',
        'dni'          => '19063550',
        'puesto'       => 'Empleado',
        'area'         => 'General',
        'lider_id'     => 3,
        'fecha_ingreso'=> '2026-01-01',
        'sueldo'       => 0,
        'email'        => 'hazcurra@llerena.com',
        'whatsapp'     => '+573106512999',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 15,
        'dias_vacaciones_usados'  => 0,
        'dias_franco_disponibles' => 3,
        'dias_franco_usados'      => 0,
    ],
    9 => [
        'id'           => 9,
        'nombre'       => 'Hernan Repetto',
        'dni'          => '19063552',
        'puesto'       => 'Empleado',
        'area'         => 'General',
        'lider_id'     => 3,
        'fecha_ingreso'=> '2026-01-01',
        'sueldo'       => 0,
        'email'        => 'hrepetto@llerena.com',
        'whatsapp'     => '+5491144043309',
        'estado'       => 'activo',
        'dias_vacaciones_anuales' => 15,
        'dias_vacaciones_usados'  => 0,
        'dias_franco_disponibles' => 3,
        'dias_franco_usados'      => 0,
    ],
];

// ── Líderes ───────────────────────────────────────
$LIDERES = [
    1 => ['nombre' => 'Carlos Ibáñez',  'area' => 'Derecho Laboral'],
    2 => ['nombre' => 'Sofía Méndez',   'area' => 'Derecho Civil'],
    3 => ['nombre' => 'RRHH General',   'area' => 'General'],
];

// ── Tipos de solicitud ────────────────────────────
$TIPOS_SOLICITUD = [
    'vacaciones'   => ['label' => 'Vacaciones',          'icon' => '🏖️',  'requiere_lider' => true,  'requiere_rrhh' => true],
    'franco'       => ['label' => 'Franco Compensatorio','icon' => '📅',  'requiere_lider' => true,  'requiere_rrhh' => false],
    'permiso'      => ['label' => 'Permiso Especial',    'icon' => '📋',  'requiere_lider' => true,  'requiere_rrhh' => true],
    'certificado'  => ['label' => 'Certificado Laboral', 'icon' => '📄',  'requiere_lider' => false, 'requiere_rrhh' => true],
    'licencia'     => ['label' => 'Licencia Médica',     'icon' => '🏥',  'requiere_lider' => false, 'requiere_rrhh' => true],
];

// ── Estados ───────────────────────────────────────
$ESTADOS = [
    'pendiente_lider' => ['label' => 'Pendiente Líder',  'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.15)'],
    'pendiente_rrhh'  => ['label' => 'Pendiente RRHH',   'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,.15)'],
    'aprobada'        => ['label' => 'Aprobada',          'color' => '#22c55e', 'bg' => 'rgba(34,197,94,.15)'],
    'rechazada'       => ['label' => 'Rechazada',         'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.15)'],
    'cancelada'       => ['label' => 'Cancelada',         'color' => '#6b7280', 'bg' => 'rgba(107,114,128,.15)'],
];

// ── Solicitudes mock ──────────────────────────────
$SOLICITUDES = [
    1 => [
        'id'           => 1,
        'codigo'       => 'VAC-2026-001',
        'tipo'         => 'vacaciones',
        'empleado_id'  => 1,
        'estado'       => 'aprobada',
        'fecha_inicio' => '2026-03-10',
        'fecha_fin'    => '2026-03-14',
        'dias'         => 5,
        'motivo'       => 'Vacaciones de verano',
        'created_at'   => '2026-02-28 09:15:00',
        'updated_at'   => '2026-02-28 14:30:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-02-28 09:15:00', 'actor' => 'MAX (bot)',      'accion' => 'Solicitud recibida vía WhatsApp. Días disponibles verificados (12/15).', 'estado' => 'pendiente_lider'],
            ['fecha' => '2026-02-28 10:02:00', 'actor' => 'Carlos Ibáñez', 'accion' => 'Aprobado por el líder vía WhatsApp.', 'estado' => 'pendiente_rrhh'],
            ['fecha' => '2026-02-28 14:30:00', 'actor' => 'María Fernández','accion' => 'Confirmado por RRHH. Días descontados del legajo.', 'estado' => 'aprobada'],
        ],
    ],
    2 => [
        'id'           => 2,
        'codigo'       => 'FRA-2026-001',
        'tipo'         => 'franco',
        'empleado_id'  => 2,
        'estado'       => 'aprobada',
        'fecha_inicio' => '2026-03-21',
        'fecha_fin'    => '2026-03-21',
        'dias'         => 1,
        'motivo'       => 'Franco por hora extra del 15/03',
        'created_at'   => '2026-03-18 16:40:00',
        'updated_at'   => '2026-03-19 09:10:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-03-18 16:40:00', 'actor' => 'MAX (bot)',      'accion' => 'Solicitud de franco recibida.', 'estado' => 'pendiente_lider'],
            ['fecha' => '2026-03-19 09:10:00', 'actor' => 'Carlos Ibáñez', 'accion' => 'Franco aprobado.', 'estado' => 'aprobada'],
        ],
    ],
    3 => [
        'id'           => 3,
        'codigo'       => 'CER-2026-001',
        'tipo'         => 'certificado',
        'empleado_id'  => 3,
        'estado'       => 'aprobada',
        'fecha_inicio' => '2026-03-25',
        'fecha_fin'    => '2026-03-25',
        'dias'         => 0,
        'motivo'       => 'Certificado laboral para trámite bancario',
        'created_at'   => '2026-03-25 08:30:00',
        'updated_at'   => '2026-03-25 09:00:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-03-25 08:30:00', 'actor' => 'MAX (bot)',       'accion' => 'Solicitud de certificado laboral recibida.', 'estado' => 'pendiente_rrhh'],
            ['fecha' => '2026-03-25 09:00:00', 'actor' => 'María Fernández', 'accion' => 'Certificado generado y enviado por WhatsApp al empleado.', 'estado' => 'aprobada'],
        ],
    ],
    4 => [
        'id'           => 4,
        'codigo'       => 'VAC-2026-002',
        'tipo'         => 'vacaciones',
        'empleado_id'  => 1,
        'estado'       => 'pendiente_lider',
        'fecha_inicio' => '2026-04-15',
        'fecha_fin'    => '2026-04-17',
        'dias'         => 3,
        'motivo'       => 'Viaje familiar',
        'created_at'   => '2026-04-05 10:20:00',
        'updated_at'   => '2026-04-05 10:20:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-04-05 10:20:00', 'actor' => 'MAX (bot)', 'accion' => 'Solicitud recibida vía WhatsApp. Días disponibles: 9/15. Aguardando aprobación del líder.', 'estado' => 'pendiente_lider'],
        ],
    ],
    5 => [
        'id'           => 5,
        'codigo'       => 'PER-2026-001',
        'tipo'         => 'permiso',
        'empleado_id'  => 4,
        'estado'       => 'rechazada',
        'fecha_inicio' => '2026-03-30',
        'fecha_fin'    => '2026-04-01',
        'dias'         => 2,
        'motivo'       => 'Mudanza',
        'created_at'   => '2026-03-27 11:00:00',
        'updated_at'   => '2026-03-27 15:45:00',
        'canal'        => 'portal',
        'historial'    => [
            ['fecha' => '2026-03-27 11:00:00', 'actor' => 'MAX (bot)',      'accion' => 'Solicitud de permiso recibida.', 'estado' => 'pendiente_lider'],
            ['fecha' => '2026-03-27 15:45:00', 'actor' => 'Sofía Méndez',  'accion' => 'Rechazado: período de cierre de mes, no es posible ausentarse.', 'estado' => 'rechazada'],
        ],
    ],
    6 => [
        'id'           => 6,
        'codigo'       => 'LIC-2026-001',
        'tipo'         => 'licencia',
        'empleado_id'  => 5,
        'estado'       => 'pendiente_rrhh',
        'fecha_inicio' => '2026-04-07',
        'fecha_fin'    => '2026-04-09',
        'dias'         => 3,
        'motivo'       => 'Reposo médico — certificado adjunto',
        'created_at'   => '2026-04-06 07:50:00',
        'updated_at'   => '2026-04-06 08:00:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-04-06 07:50:00', 'actor' => 'MAX (bot)', 'accion' => 'Licencia médica recibida. Derivada directamente a RRHH.', 'estado' => 'pendiente_rrhh'],
        ],
    ],
    7 => [
        'id'           => 7,
        'codigo'       => 'VAC-2026-003',
        'tipo'         => 'vacaciones',
        'empleado_id'  => 6,
        'estado'       => 'aprobada',
        'fecha_inicio' => '2026-02-01',
        'fecha_fin'    => '2026-02-07',
        'dias'         => 7,
        'motivo'       => 'Vacaciones anuales',
        'created_at'   => '2026-01-20 09:00:00',
        'updated_at'   => '2026-01-21 10:00:00',
        'canal'        => 'portal',
        'historial'    => [
            ['fecha' => '2026-01-20 09:00:00', 'actor' => 'María Fernández', 'accion' => 'Solicitud creada desde portal.', 'estado' => 'pendiente_rrhh'],
            ['fecha' => '2026-01-21 10:00:00', 'actor' => 'María Fernández', 'accion' => 'Aprobada y comunicada al empleado.', 'estado' => 'aprobada'],
        ],
    ],
    8 => [
        'id'           => 8,
        'codigo'       => 'FRA-2026-002',
        'tipo'         => 'franco',
        'empleado_id'  => 3,
        'estado'       => 'pendiente_rrhh',
        'fecha_inicio' => '2026-04-10',
        'fecha_fin'    => '2026-04-10',
        'dias'         => 1,
        'motivo'       => 'Franco por guardia del sábado',
        'created_at'   => '2026-04-05 18:30:00',
        'updated_at'   => '2026-04-05 19:00:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-04-05 18:30:00', 'actor' => 'MAX (bot)',     'accion' => 'Solicitud de franco recibida.', 'estado' => 'pendiente_lider'],
            ['fecha' => '2026-04-05 19:00:00', 'actor' => 'Sofía Méndez', 'accion' => 'Aprobado por la líder.', 'estado' => 'pendiente_rrhh'],
        ],
    ],
    9 => [
        'id'           => 9,
        'codigo'       => 'CER-2026-002',
        'tipo'         => 'certificado',
        'empleado_id'  => 2,
        'estado'       => 'pendiente_rrhh',
        'fecha_inicio' => '2026-04-06',
        'fecha_fin'    => '2026-04-06',
        'dias'         => 0,
        'motivo'       => 'Certificado para obra social',
        'created_at'   => '2026-04-06 09:00:00',
        'updated_at'   => '2026-04-06 09:00:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-04-06 09:00:00', 'actor' => 'MAX (bot)', 'accion' => 'Solicitud de certificado recibida. Enviada a RRHH.', 'estado' => 'pendiente_rrhh'],
        ],
    ],
    10 => [
        'id'           => 10,
        'codigo'       => 'VAC-2026-004',
        'tipo'         => 'vacaciones',
        'empleado_id'  => 4,
        'estado'       => 'pendiente_lider',
        'fecha_inicio' => '2026-04-20',
        'fecha_fin'    => '2026-04-22',
        'dias'         => 3,
        'motivo'       => 'Semana Santa',
        'created_at'   => '2026-04-06 08:10:00',
        'updated_at'   => '2026-04-06 08:10:00',
        'canal'        => 'whatsapp',
        'historial'    => [
            ['fecha' => '2026-04-06 08:10:00', 'actor' => 'MAX (bot)', 'accion' => 'Solicitud recibida. Aguardando aprobación del líder.', 'estado' => 'pendiente_lider'],
        ],
    ],
];

// ── Helpers ───────────────────────────────────────

function requireLogin() {
    if (!isset($_SESSION['portal_user'])) {
        header('Location: login.php');
        exit;
    }
}

function requireRol(array $roles) {
    requireLogin();
    $user = $_SESSION['portal_user'];
    if (!in_array($user['rol'], $roles)) {
        header('Location: dashboard.php?error=acceso_denegado');
        exit;
    }
}

function currentUser(): array {
    return $_SESSION['portal_user'] ?? [];
}

function rolLabel(string $rol): string {
    $map = [
        'rrhh'     => 'Recursos Humanos',
        'lider'    => 'Líder de Equipo',
        'empleado' => 'Empleado',
        'finanzas' => 'Finanzas',
    ];
    return $map[$rol] ?? ucfirst($rol);
}

function estadoBadge(string $estado): string {
    global $ESTADOS;
    $e = $ESTADOS[$estado] ?? ['label' => $estado, 'color' => '#888', 'bg' => '#eee'];
    return '<span class="badge" style="background:'.$e['bg'].';color:'.$e['color'].'">'
         . htmlspecialchars($e['label'])
         . '</span>';
}

function formatDate(string $d): string {
    if (!$d) return '—';
    $dt = DateTime::createFromFormat('Y-m-d', $d)
       ?: DateTime::createFromFormat('Y-m-d H:i:s', $d);
    return $dt ? $dt->format('d/m/Y') : $d;
}

function formatDateTime(string $d): string {
    if (!$d) return '—';
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $d);
    return $dt ? $dt->format('d/m/Y H:i') : $d;
}

function tipoLabel(string $tipo): string {
    global $TIPOS_SOLICITUD;
    $t = $TIPOS_SOLICITUD[$tipo] ?? null;
    return $t ? $t['icon'].' '.$t['label'] : $tipo;
}

function empleadoNombre(int $id): string {
    global $EMPLEADOS;
    return $EMPLEADOS[$id]['nombre'] ?? '—';
}

function solicitudesFiltradas(): array {
    global $SOLICITUDES, $EMPLEADOS;
    $u = currentUser();
    $rol = $u['rol'];

    if ($rol === 'rrhh' || $rol === 'finanzas') {
        return $SOLICITUDES;
    }

    if ($rol === 'lider') {
        $area = $u['area'] ?? '';
        return array_filter($SOLICITUDES, function($s) use ($EMPLEADOS, $area) {
            $emp = $EMPLEADOS[$s['empleado_id']] ?? null;
            return $emp && $emp['area'] === $area;
        });
    }

    if ($rol === 'empleado') {
        $eid = $u['empleado_id'] ?? null;
        return array_filter($SOLICITUDES, fn($s) => $s['empleado_id'] === $eid);
    }

    return [];
}

// ── Log de consultas MAX (mock para demo) ─────────────────────
$LOG_CONSULTAS = [
    ['id'=>1,  'empleado_id'=>1, 'numero_whatsapp'=>'5491134567890', 'tipo_consulta'=>'saludo',          'mensaje_usuario'=>'Hola',                                    'respuesta_bot'=>'Hola Laura! Tenés 9 días de vacaciones disponibles...', 'solicitud_id'=>null, 'created_at'=>'2026-04-06 08:12:00'],
    ['id'=>2,  'empleado_id'=>1, 'numero_whatsapp'=>'5491134567890', 'tipo_consulta'=>'vacaciones',       'mensaje_usuario'=>'Quiero pedir vacaciones del 15 al 17 de abril', 'respuesta_bot'=>'Perfecto Laura, solicitud de vacaciones creada.', 'solicitud_id'=>4, 'created_at'=>'2026-04-06 08:14:00'],
    ['id'=>3,  'empleado_id'=>2, 'numero_whatsapp'=>'5491145678901', 'tipo_consulta'=>'consulta_dias',    'mensaje_usuario'=>'Cuántos días de vacaciones me quedan?',    'respuesta_bot'=>'Hola Tomás! Tenés 7 días disponibles de 10 anuales.', 'solicitud_id'=>null, 'created_at'=>'2026-04-06 09:05:00'],
    ['id'=>4,  'empleado_id'=>2, 'numero_whatsapp'=>'5491145678901', 'tipo_consulta'=>'certificado',      'mensaje_usuario'=>'Necesito un certificado laboral para el banco', 'respuesta_bot'=>'Entendido, solicitud de certificado creada.', 'solicitud_id'=>9, 'created_at'=>'2026-04-06 09:08:00'],
    ['id'=>5,  'empleado_id'=>3, 'numero_whatsapp'=>'5491156789012', 'tipo_consulta'=>'franco',           'mensaje_usuario'=>'Quiero tomar el franco del jueves',        'respuesta_bot'=>'Confirmado, franco para el 10/04 enviado a tu líder.', 'solicitud_id'=>8, 'created_at'=>'2026-04-05 18:32:00'],
    ['id'=>6,  'empleado_id'=>5, 'numero_whatsapp'=>'5491178901234', 'tipo_consulta'=>'licencia',         'mensaje_usuario'=>'Estoy enferma, tengo certificado médico',  'respuesta_bot'=>'Entendido Camila, licencia médica registrada.', 'solicitud_id'=>6, 'created_at'=>'2026-04-06 07:52:00'],
    ['id'=>7,  'empleado_id'=>4, 'numero_whatsapp'=>'5491167890123', 'tipo_consulta'=>'vacaciones',       'mensaje_usuario'=>'Pedir vacaciones para semana santa',       'respuesta_bot'=>'Solicitud creada para el 20-22/04, pendiente aprobación.', 'solicitud_id'=>10, 'created_at'=>'2026-04-06 08:11:00'],
    ['id'=>8,  'empleado_id'=>1, 'numero_whatsapp'=>'5491134567890', 'tipo_consulta'=>'consulta_estado',  'mensaje_usuario'=>'Cómo está mi solicitud de vacaciones?',    'respuesta_bot'=>'Tu solicitud VAC-2026-002 está pendiente de aprobación del líder.', 'solicitud_id'=>null, 'created_at'=>'2026-04-06 10:30:00'],
    ['id'=>9,  'empleado_id'=>6, 'numero_whatsapp'=>'5491189012345', 'tipo_consulta'=>'consulta_dias',    'mensaje_usuario'=>'Días disponibles?',                        'respuesta_bot'=>'Hola Diego! Tenés 14 días de vacaciones y 5 francos.', 'solicitud_id'=>null, 'created_at'=>'2026-04-05 14:20:00'],
    ['id'=>10, 'empleado_id'=>3, 'numero_whatsapp'=>'5491156789012', 'tipo_consulta'=>'certificado',      'mensaje_usuario'=>'Necesito certificado para obra social',    'respuesta_bot'=>'Solicitud de certificado creada, RRHH lo enviará en breve.', 'solicitud_id'=>null, 'created_at'=>'2026-04-04 11:15:00'],
    ['id'=>11, 'empleado_id'=>2, 'numero_whatsapp'=>'5491145678901', 'tipo_consulta'=>'saludo',           'mensaje_usuario'=>'Buenos días',                              'respuesta_bot'=>'Buenos días Tomás! ¿En qué te puedo ayudar hoy?', 'solicitud_id'=>null, 'created_at'=>'2026-04-03 08:00:00'],
    ['id'=>12, 'empleado_id'=>5, 'numero_whatsapp'=>'5491178901234', 'tipo_consulta'=>'consulta_dias',    'mensaje_usuario'=>'Mis vacaciones disponibles',               'respuesta_bot'=>'Tenés 6 días de vacaciones disponibles de 15 anuales.', 'solicitud_id'=>null, 'created_at'=>'2026-04-03 09:30:00'],
    ['id'=>13, 'empleado_id'=>1, 'numero_whatsapp'=>'5491134567890', 'tipo_consulta'=>'vacaciones',       'mensaje_usuario'=>'Quiero vacaciones en marzo',               'respuesta_bot'=>'Vacaciones del 10 al 14 de marzo procesadas correctamente.', 'solicitud_id'=>1, 'created_at'=>'2026-02-28 09:18:00'],
    ['id'=>14, 'empleado_id'=>2, 'numero_whatsapp'=>'5491145678901', 'tipo_consulta'=>'franco',           'mensaje_usuario'=>'Franco compensatorio para el viernes',    'respuesta_bot'=>'Franco del 21/03 aprobado por tu líder.', 'solicitud_id'=>2, 'created_at'=>'2026-03-18 16:42:00'],
    ['id'=>15, 'empleado_id'=>3, 'numero_whatsapp'=>'5491156789012', 'tipo_consulta'=>'certificado',      'mensaje_usuario'=>'Certificado laboral urgente',              'respuesta_bot'=>'Certificado generado y enviado a tu WhatsApp.', 'solicitud_id'=>3, 'created_at'=>'2026-03-25 08:32:00'],
    ['id'=>16, 'empleado_id'=>4, 'numero_whatsapp'=>'5491167890123', 'tipo_consulta'=>'saludo',           'mensaje_usuario'=>'Hola MAX',                                 'respuesta_bot'=>'Hola Martín! Tenés 7 días de vacaciones disponibles.', 'solicitud_id'=>null, 'created_at'=>'2026-04-06 08:09:00'],
    ['id'=>17, 'empleado_id'=>6, 'numero_whatsapp'=>'5491189012345', 'tipo_consulta'=>'permiso',          'mensaje_usuario'=>'Necesito permiso para ir al médico mañana', 'respuesta_bot'=>'Para permisos de menos de un día, hablá directamente con RRHH.', 'solicitud_id'=>null, 'created_at'=>'2026-04-05 16:00:00'],
    ['id'=>18, 'empleado_id'=>5, 'numero_whatsapp'=>'5491178901234', 'tipo_consulta'=>'consulta_estado',  'mensaje_usuario'=>'Estado de mi licencia médica',             'respuesta_bot'=>'Tu licencia LIC-2026-001 está pendiente de confirmación por RRHH.', 'solicitud_id'=>null, 'created_at'=>'2026-04-06 10:00:00'],
    ['id'=>19, 'empleado_id'=>1, 'numero_whatsapp'=>'5491134567890', 'tipo_consulta'=>'consulta_dias',    'mensaje_usuario'=>'Cuántos francos tengo?',                   'respuesta_bot'=>'Tenés 2 francos disponibles.', 'solicitud_id'=>null, 'created_at'=>'2026-04-02 12:00:00'],
    ['id'=>20, 'empleado_id'=>3, 'numero_whatsapp'=>'5491156789012', 'tipo_consulta'=>'vacaciones',       'mensaje_usuario'=>'Pedir vacaciones anuales febrero',         'respuesta_bot'=>'Vacaciones del 1 al 7 de febrero registradas.', 'solicitud_id'=>7, 'created_at'=>'2026-01-20 09:05:00'],
];
