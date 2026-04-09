<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
$rol  = $user['rol'];

// Stats generales
$sols = solicitudesFiltradas();
$pendientes  = count(array_filter($sols, fn($s) => in_array($s['estado'], ['pendiente_lider','pendiente_rrhh'])));
$aprobadas   = count(array_filter($sols, fn($s) => $s['estado'] === 'aprobada'));
$rechazadas  = count(array_filter($sols, fn($s) => $s['estado'] === 'rechazada'));
$total       = count($sols);

// Solicitudes recientes (últimas 5)
usort($sols, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
$recientes = array_slice($sols, 0, 5);

// Stats de consultas MAX (solo para rol RRHH)
if ($rol === 'rrhh') {
    $hoy = date('Y-m-d');
    $logHoy     = array_filter($LOG_CONSULTAS, fn($l) => substr($l['created_at'],0,10) === $hoy);
    $numUnicos  = count(array_unique(array_column($LOG_CONSULTAS, 'numero_whatsapp')));
    $porTipoLog = [];
    foreach ($LOG_CONSULTAS as $l) {
        $t = $l['tipo_consulta'];
        $porTipoLog[$t] = ($porTipoLog[$t] ?? 0) + 1;
    }
    arsort($porTipoLog);
    $topTipos = array_slice($porTipoLog, 0, 5, true);
    $totalLog = count($LOG_CONSULTAS);

    // Iconos por tipo
    $tipoIconos = [
        'vacaciones'      => '🏖️',
        'franco'          => '📅',
        'certificado'     => '📄',
        'licencia'        => '🏥',
        'permiso'         => '📋',
        'consulta_dias'   => '🔢',
        'consulta_estado' => '🔍',
        'saludo'          => '👋',
        'otro'            => '💬',
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAX · Dashboard</title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main">
    <div class="page-header">
      <div>
        <h1>Dashboard</h1>
        <p class="page-sub">Bienvenido/a, <?= htmlspecialchars($user['nombre']) ?> · <?= rolLabel($rol) ?></p>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);color:#60a5fa">📋</div>
        <div class="stat-body">
          <div class="stat-num"><?= $total ?></div>
          <div class="stat-label">Solicitudes totales</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24">⏳</div>
        <div class="stat-body">
          <div class="stat-num"><?= $pendientes ?></div>
          <div class="stat-label">Pendientes de acción</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);color:#4ade80">✅</div>
        <div class="stat-body">
          <div class="stat-num"><?= $aprobadas ?></div>
          <div class="stat-label">Aprobadas</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,.15);color:#f87171">❌</div>
        <div class="stat-body">
          <div class="stat-num"><?= $rechazadas ?></div>
          <div class="stat-label">Rechazadas</div>
        </div>
      </div>
    </div>

    <?php if ($rol === 'rrhh'): ?>
    <!-- Vista RRHH: empleados resumen -->
    <div class="section-grid">
      <div class="panel">
        <div class="panel-header">
          <h2>Solicitudes recientes</h2>
          <a href="solicitudes.php" class="panel-link">Ver todas →</a>
        </div>
        <table class="data-table">
          <thead><tr><th>Código</th><th>Empleado</th><th>Tipo</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($recientes as $s): ?>
            <tr>
              <td><code><?= htmlspecialchars($s['codigo']) ?></code></td>
              <td><?= htmlspecialchars(empleadoNombre($s['empleado_id'])) ?></td>
              <td><?= tipoLabel($s['tipo']) ?></td>
              <td><?= formatDate($s['created_at']) ?></td>
              <td><?= estadoBadge($s['estado']) ?></td>
              <td><a href="detalle-solicitud.php?id=<?= $s['id'] ?>" class="tbl-link">Ver</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Accesos rápidos</h2>
        </div>
        <div class="quick-links">
          <a href="solicitudes.php?estado=pendiente_rrhh" class="ql-item">
            <span class="ql-icon">⏳</span>
            <div><strong>Pendientes RRHH</strong><small>Solicitudes que requieren tu acción</small></div>
          </a>
          <a href="empleados.php" class="ql-item">
            <span class="ql-icon">👥</span>
            <div><strong>Legajos</strong><small>Ver todos los empleados</small></div>
          </a>
          <a href="reportes.php" class="ql-item">
            <span class="ql-icon">📊</span>
            <div><strong>Reportes</strong><small>Métricas y estadísticas</small></div>
          </a>
          <a href="historial.php" class="ql-item">
            <span class="ql-icon">📅</span>
            <div><strong>Control de días</strong><small>Vacaciones y francos por empleado</small></div>
          </a>
        </div>
      </div>
    </div>

    <!-- Panel de consultas MAX — solo RRHH -->
    <div class="section-grid" style="margin-top:0">

      <!-- Stat cards MAX -->
      <div class="panel">
        <div class="panel-header">
          <h2>📱 Consultas MAX (WhatsApp)</h2>
          <span style="font-size:.75rem;color:var(--gray)">Últimas interacciones</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:1.1rem">
          <div style="background:rgba(34,197,94,.07);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:.8rem;text-align:center">
            <div style="font-size:1.6rem;font-weight:800;color:#4ade80"><?= count($logHoy) ?></div>
            <div style="font-size:.72rem;color:var(--gray)">Consultas hoy</div>
          </div>
          <div style="background:rgba(59,130,246,.07);border:1px solid rgba(59,130,246,.2);border-radius:10px;padding:.8rem;text-align:center">
            <div style="font-size:1.6rem;font-weight:800;color:#60a5fa"><?= $totalLog ?></div>
            <div style="font-size:.72rem;color:var(--gray)">Total consultas</div>
          </div>
          <div style="background:rgba(6,182,212,.07);border:1px solid rgba(6,182,212,.2);border-radius:10px;padding:.8rem;text-align:center">
            <div style="font-size:1.6rem;font-weight:800;color:#67e8f9"><?= $numUnicos ?></div>
            <div style="font-size:.72rem;color:var(--gray)">Usuarios únicos</div>
          </div>
        </div>

        <!-- Top tipos -->
        <div style="font-size:.75rem;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.6rem">Top consultas por tipo</div>
        <div style="display:flex;flex-direction:column;gap:.5rem">
          <?php foreach ($topTipos as $tipo => $count):
            $pct = $totalLog > 0 ? round($count/$totalLog*100) : 0;
            $icono = $tipoIconos[$tipo] ?? '💬';
            $colors = ['vacaciones'=>'#3b82f6','franco'=>'#06b6d4','certificado'=>'#8b5cf6','licencia'=>'#ef4444','permiso'=>'#f59e0b','consulta_dias'=>'#22c55e','consulta_estado'=>'#60a5fa','saludo'=>'#fbbf24','otro'=>'#94a3b8'];
            $col = $colors[$tipo] ?? '#94a3b8';
          ?>
          <div style="display:flex;align-items:center;gap:.6rem;font-size:.8rem">
            <span style="width:22px;text-align:center"><?= $icono ?></span>
            <span style="width:120px;color:var(--gray);text-transform:capitalize"><?= htmlspecialchars(str_replace('_',' ',$tipo)) ?></span>
            <div style="flex:1;height:6px;background:rgba(255,255,255,.07);border-radius:3px">
              <div style="height:100%;width:<?= $pct ?>%;background:<?= $col ?>;border-radius:3px"></div>
            </div>
            <span style="width:30px;text-align:right;font-weight:700"><?= $count ?></span>
            <span style="color:var(--gray);width:35px"><?= $pct ?>%</span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Log reciente -->
      <div class="panel">
        <div class="panel-header">
          <h2>Últimas interacciones</h2>
          <span style="font-size:.75rem;color:var(--gray)">Conversaciones recientes con MAX</span>
        </div>
        <?php
        $logsRecientes = $LOG_CONSULTAS;
        usort($logsRecientes, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
        $logsRecientes = array_slice($logsRecientes, 0, 6);
        ?>
        <div style="display:flex;flex-direction:column;gap:.6rem">
          <?php foreach ($logsRecientes as $log):
            $icono = $tipoIconos[$log['tipo_consulta']] ?? '💬';
          ?>
          <div style="display:flex;align-items:flex-start;gap:.75rem;padding:.6rem 0;border-bottom:1px solid rgba(255,255,255,.04)">
            <span style="font-size:1.1rem;flex-shrink:0;margin-top:.05rem"><?= $icono ?></span>
            <div style="flex:1;min-width:0">
              <div style="font-size:.8rem;font-weight:600"><?= htmlspecialchars(empleadoNombre($log['empleado_id'])) ?></div>
              <div style="font-size:.77rem;color:var(--gray);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars(mb_strimwidth($log['mensaje_usuario'], 0, 55, '…')) ?>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
              <div style="font-size:.7rem;color:var(--gray)"><?= formatDateTime($log['created_at']) ?></div>
              <?php if ($log['solicitud_id']): ?>
                <span class="badge" style="background:rgba(34,197,94,.1);color:#4ade80;margin-top:.2rem">→ solicitud</span>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /panel consultas MAX -->

    <?php elseif ($rol === 'lider'): ?>
    <!-- Vista Líder -->
    <div class="section-grid">
      <div class="panel">
        <div class="panel-header">
          <h2>Solicitudes de tu equipo</h2>
          <a href="solicitudes.php" class="panel-link">Ver todas →</a>
        </div>
        <table class="data-table">
          <thead><tr><th>Código</th><th>Empleado</th><th>Tipo</th><th>Días</th><th>Estado</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($recientes as $s): ?>
            <tr>
              <td><code><?= htmlspecialchars($s['codigo']) ?></code></td>
              <td><?= htmlspecialchars(empleadoNombre($s['empleado_id'])) ?></td>
              <td><?= tipoLabel($s['tipo']) ?></td>
              <td><?= $s['dias'] ?: '—' ?></td>
              <td><?= estadoBadge($s['estado']) ?></td>
              <td><a href="detalle-solicitud.php?id=<?= $s['id'] ?>" class="tbl-link">Ver</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="panel">
        <div class="panel-header"><h2>Tu equipo</h2></div>
        <div class="team-list">
        <?php
        global $EMPLEADOS;
        foreach ($EMPLEADOS as $e) {
            if ($e['area'] !== ($user['area'] ?? '')) continue;
            $diasLib = $e['dias_vacaciones_anuales'] - $e['dias_vacaciones_usados'];
        ?>
          <div class="team-item">
            <div class="team-avatar"><?= strtoupper(substr($e['nombre'],0,1)) ?></div>
            <div class="team-info">
              <strong><?= htmlspecialchars($e['nombre']) ?></strong>
              <small><?= htmlspecialchars($e['puesto']) ?></small>
            </div>
            <div class="team-days">
              <span class="badge" style="background:rgba(34,197,94,.12);color:#4ade80"><?= $diasLib ?>d vac</span>
            </div>
          </div>
        <?php } ?>
        </div>
      </div>
    </div>

    <?php elseif ($rol === 'empleado'): ?>
    <!-- Vista Empleado -->
    <?php
    global $EMPLEADOS;
    $eid = $user['empleado_id'] ?? null;
    $emp = $eid ? ($EMPLEADOS[$eid] ?? null) : null;
    if ($emp):
      $vacLib   = $emp['dias_vacaciones_anuales'] - $emp['dias_vacaciones_usados'];
      $francoLib= $emp['dias_franco_disponibles'] - $emp['dias_franco_usados'];
    ?>
    <div class="section-grid">
      <div class="panel">
        <div class="panel-header"><h2>Mi legajo</h2></div>
        <div class="legajo-mini">
          <div class="lm-row"><span>Puesto</span><strong><?= htmlspecialchars($emp['puesto']) ?></strong></div>
          <div class="lm-row"><span>Área</span><strong><?= htmlspecialchars($emp['area']) ?></strong></div>
          <div class="lm-row"><span>Ingreso</span><strong><?= formatDate($emp['fecha_ingreso']) ?></strong></div>
          <div class="lm-row"><span>Email</span><strong><?= htmlspecialchars($emp['email']) ?></strong></div>
        </div>
        <div class="dias-bars">
          <div class="dias-bar-item">
            <div class="db-label">
              <span>Vacaciones</span>
              <strong><?= $vacLib ?> / <?= $emp['dias_vacaciones_anuales'] ?> días</strong>
            </div>
            <div class="db-track">
              <div class="db-fill" style="width:<?= round($vacLib/$emp['dias_vacaciones_anuales']*100) ?>%;background:#3b82f6"></div>
            </div>
          </div>
          <div class="dias-bar-item">
            <div class="db-label">
              <span>Francos</span>
              <strong><?= $francoLib ?> / <?= $emp['dias_franco_disponibles'] ?> días</strong>
            </div>
            <div class="db-track">
              <div class="db-fill" style="width:<?= $emp['dias_franco_disponibles'] > 0 ? round($francoLib/$emp['dias_franco_disponibles']*100) : 0 ?>%;background:#06b6d4"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Mis solicitudes</h2>
          <a href="solicitudes.php" class="panel-link">Ver todas →</a>
        </div>
        <table class="data-table">
          <thead><tr><th>Código</th><th>Tipo</th><th>Fechas</th><th>Estado</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($recientes as $s): ?>
            <tr>
              <td><code><?= htmlspecialchars($s['codigo']) ?></code></td>
              <td><?= tipoLabel($s['tipo']) ?></td>
              <td><?= formatDate($s['fecha_inicio']) ?><?= $s['fecha_fin'] !== $s['fecha_inicio'] ? ' – '.formatDate($s['fecha_fin']) : '' ?></td>
              <td><?= estadoBadge($s['estado']) ?></td>
              <td><a href="detalle-solicitud.php?id=<?= $s['id'] ?>" class="tbl-link">Ver</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <?php elseif ($rol === 'finanzas'): ?>
    <!-- Vista Finanzas -->
    <?php
    global $EMPLEADOS;
    $costoTotal = 0;
    foreach ($EMPLEADOS as $e) { $costoTotal += $e['sueldo']; }
    ?>
    <div class="section-grid">
      <div class="panel">
        <div class="panel-header"><h2>Resumen de nómina</h2></div>
        <div class="fin-summary">
          <div class="fin-item">
            <div class="fin-num">$<?= number_format($costoTotal, 0, ',', '.') ?></div>
            <div class="fin-label">Costo total nómina mensual</div>
          </div>
          <div class="fin-item">
            <div class="fin-num"><?= count($EMPLEADOS) ?></div>
            <div class="fin-label">Empleados activos</div>
          </div>
          <div class="fin-item">
            <div class="fin-num"><?= $aprobadas ?></div>
            <div class="fin-label">Ausencias aprobadas (período)</div>
          </div>
        </div>
        <a href="reportes.php" class="btn-primary" style="display:inline-block;margin-top:1.25rem">Ver reporte completo →</a>
      </div>

      <div class="panel">
        <div class="panel-header"><h2>Ausencias aprobadas recientes</h2></div>
        <table class="data-table">
          <thead><tr><th>Empleado</th><th>Tipo</th><th>Días</th><th>Desde</th></tr></thead>
          <tbody>
          <?php foreach ($recientes as $s):
                if ($s['estado'] !== 'aprobada') continue; ?>
            <tr>
              <td><?= htmlspecialchars(empleadoNombre($s['empleado_id'])) ?></td>
              <td><?= tipoLabel($s['tipo']) ?></td>
              <td><?= $s['dias'] ?: '—' ?></td>
              <td><?= formatDate($s['fecha_inicio']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </main>
</div>

<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
</body>
</html>
