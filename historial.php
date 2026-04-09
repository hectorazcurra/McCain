<?php
require_once __DIR__ . '/config.php';
requireRol(['rrhh','lider','finanzas']);
$user = currentUser();
$rol  = $user['rol'];

// Filtrar empleados según rol
$lista = $EMPLEADOS;
if ($rol === 'lider') {
    $lista = array_filter($lista, fn($e) => $e['area'] === ($user['area'] ?? ''));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAX · Control de Días</title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
<style>
.hist-table-wrap { overflow-x: auto; }
.progress-cell { min-width: 120px; }
.progress-bar { height: 6px; background: rgba(255,255,255,.07); border-radius: 3px; margin-top: .25rem; }
.progress-fill { height: 100%; border-radius: 3px; }
</style>
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>
<div class="layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <main class="main">

    <div class="page-header">
      <div>
        <h1>Control de Días</h1>
        <p class="page-sub">Vacaciones y francos por empleado · Período <?= date('Y') ?></p>
      </div>
    </div>

    <!-- Resumen de totales -->
    <?php
    $totalVacAnual = 0; $totalVacUsados = 0;
    $totalFranDisp = 0; $totalFranUsados = 0;
    foreach ($lista as $e) {
        $totalVacAnual  += $e['dias_vacaciones_anuales'];
        $totalVacUsados += $e['dias_vacaciones_usados'];
        $totalFranDisp  += $e['dias_franco_disponibles'];
        $totalFranUsados+= $e['dias_franco_usados'];
    }
    ?>
    <div class="stats-grid" style="margin-bottom:1.75rem">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);color:#60a5fa">🏖️</div>
        <div class="stat-body">
          <div class="stat-num"><?= $totalVacAnual - $totalVacUsados ?></div>
          <div class="stat-label">Días vac. disponibles (equipo)</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24">📋</div>
        <div class="stat-body">
          <div class="stat-num"><?= $totalVacUsados ?></div>
          <div class="stat-label">Días vac. consumidos</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(6,182,212,.15);color:#67e8f9">📅</div>
        <div class="stat-body">
          <div class="stat-num"><?= $totalFranDisp - $totalFranUsados ?></div>
          <div class="stat-label">Francos disponibles (equipo)</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);color:#4ade80">✅</div>
        <div class="stat-body">
          <div class="stat-num"><?= $totalFranUsados ?></div>
          <div class="stat-label">Francos consumidos</div>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header"><h2>Detalle por empleado</h2></div>
      <div class="hist-table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Empleado</th>
              <th>Área</th>
              <?php if ($rol === 'rrhh' || $rol === 'finanzas'): ?><th>Sueldo</th><?php endif; ?>
              <th>Vac. anuales</th>
              <th>Vac. usadas</th>
              <th>Vac. disponibles</th>
              <th class="progress-cell">% Consumo vac.</th>
              <th>Franco disp.</th>
              <th>Franco usado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($lista as $emp):
            $vacLib = $emp['dias_vacaciones_anuales'] - $emp['dias_vacaciones_usados'];
            $vacPct = $emp['dias_vacaciones_anuales'] > 0
                    ? round($emp['dias_vacaciones_usados'] / $emp['dias_vacaciones_anuales'] * 100) : 0;
            $fraLib = $emp['dias_franco_disponibles'] - $emp['dias_franco_usados'];
            $color  = $vacPct >= 90 ? '#f87171' : ($vacPct >= 60 ? '#fbbf24' : '#4ade80');
          ?>
          <tr>
            <td><strong><?= htmlspecialchars($emp['nombre']) ?></strong></td>
            <td><?= htmlspecialchars($emp['area']) ?></td>
            <?php if ($rol === 'rrhh' || $rol === 'finanzas'): ?>
              <td>$<?= number_format($emp['sueldo'],0,',','.') ?></td>
            <?php endif; ?>
            <td><?= $emp['dias_vacaciones_anuales'] ?></td>
            <td><?= $emp['dias_vacaciones_usados'] ?></td>
            <td>
              <span style="font-weight:700;color:<?= $vacLib <= 3 ? '#f87171' : 'inherit' ?>">
                <?= $vacLib ?>
              </span>
            </td>
            <td class="progress-cell">
              <span style="font-size:.78rem;color:<?= $color ?>"><?= $vacPct ?>%</span>
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $vacPct ?>%;background:<?= $color ?>"></div>
              </div>
            </td>
            <td><?= $emp['dias_franco_disponibles'] ?></td>
            <td><?= $emp['dias_franco_usados'] ?></td>
            <td><a href="empleados.php?id=<?= $emp['id'] ?>" class="tbl-link">Legajo →</a></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Solicitudes aprobadas por empleado -->
    <div class="panel" style="margin-top:1.25rem">
      <div class="panel-header"><h2>Ausencias aprobadas en el período</h2></div>
      <?php
      $aprobadas = array_filter($SOLICITUDES, function($s) use ($lista, $rol) {
          if ($s['estado'] !== 'aprobada') return false;
          if ($rol === 'lider') return isset($lista[$s['empleado_id']]);
          return true;
      });
      usort($aprobadas, fn($a,$b) => strcmp($b['fecha_inicio'], $a['fecha_inicio']));
      if (empty($aprobadas)):
      ?>
        <p style="font-size:.875rem;color:var(--gray)">Sin ausencias aprobadas registradas.</p>
      <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Empleado</th><th>Tipo</th><th>Desde</th><th>Hasta</th><th>Días</th><th>Aprobada</th></tr></thead>
        <tbody>
          <?php foreach ($aprobadas as $s): ?>
          <tr>
            <td><?= htmlspecialchars(empleadoNombre($s['empleado_id'])) ?></td>
            <td><?= tipoLabel($s['tipo']) ?></td>
            <td><?= formatDate($s['fecha_inicio']) ?></td>
            <td><?= formatDate($s['fecha_fin']) ?></td>
            <td><?= $s['dias'] ?: '—' ?></td>
            <td style="color:var(--gray)"><?= formatDateTime($s['updated_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

  </main>
</div>
<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
</body>
</html>
