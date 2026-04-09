<?php
require_once __DIR__ . '/config.php';
requireRol(['rrhh','finanzas']);
$user = currentUser();
$rol  = $user['rol'];

// Cálculos
$totalNomina = array_sum(array_column($EMPLEADOS, 'sueldo'));
$countEmp    = count($EMPLEADOS);

// Por tipo
$porTipo = [];
foreach ($TIPOS_SOLICITUD as $k => $t) {
    $porTipo[$k] = count(array_filter($SOLICITUDES, fn($s) => $s['tipo'] === $k));
}
// Por estado
$porEstado = [];
foreach ($ESTADOS as $k => $e) {
    $porEstado[$k] = count(array_filter($SOLICITUDES, fn($s) => $s['estado'] === $k));
}
// Por canal
$whatsapp = count(array_filter($SOLICITUDES, fn($s) => $s['canal'] === 'whatsapp'));
$portal   = count(array_filter($SOLICITUDES, fn($s) => $s['canal'] === 'portal'));
$total    = count($SOLICITUDES);

// Dias ausencia aprobados
$diasAusencia = 0;
foreach ($SOLICITUDES as $s) {
    if ($s['estado'] === 'aprobada') $diasAusencia += $s['dias'];
}
// Costo estimado ausencias (sueldo diario = sueldo/22)
$costoAusencia = 0;
foreach ($SOLICITUDES as $s) {
    if ($s['estado'] === 'aprobada' && $s['dias'] > 0) {
        $emp = $EMPLEADOS[$s['empleado_id']] ?? null;
        if ($emp) $costoAusencia += ($emp['sueldo'] / 22) * $s['dias'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAX · Reportes</title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
<style>
.report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px,1fr)); gap: 1.25rem; }
.bar-chart { display: flex; flex-direction: column; gap: .65rem; margin-top: .75rem; }
.bar-item { display: flex; align-items: center; gap: .75rem; font-size: .82rem; }
.bar-label { width: 130px; flex-shrink: 0; color: var(--gray); }
.bar-track { flex: 1; height: 8px; background: rgba(255,255,255,.07); border-radius: 4px; }
.bar-fill  { height: 100%; border-radius: 4px; transition: width .4s; }
.bar-val   { width: 35px; text-align: right; font-weight: 600; }
.donut-wrap { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; margin-top: .75rem; }
.legend    { display: flex; flex-direction: column; gap: .4rem; }
.legend-item { display: flex; align-items: center; gap: .5rem; font-size: .8rem; }
.legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
</style>
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>
<div class="layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <main class="main">

    <div class="page-header">
      <div>
        <h1>Reportes</h1>
        <p class="page-sub">Métricas del período · Datos demo</p>
      </div>
    </div>

    <!-- KPIs principales -->
    <div class="stats-grid" style="margin-bottom:1.75rem">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);color:#60a5fa">📋</div>
        <div class="stat-body">
          <div class="stat-num"><?= $total ?></div>
          <div class="stat-label">Solicitudes totales</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);color:#4ade80">✅</div>
        <div class="stat-body">
          <div class="stat-num"><?= $porEstado['aprobada'] ?></div>
          <div class="stat-label">Aprobadas (<?= $total > 0 ? round($porEstado['aprobada']/$total*100) : 0 ?>%)</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24">⏳</div>
        <div class="stat-body">
          <div class="stat-num"><?= ($porEstado['pendiente_lider'] + $porEstado['pendiente_rrhh']) ?></div>
          <div class="stat-label">Pendientes de resolución</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.12);color:#34d399">🏖️</div>
        <div class="stat-body">
          <div class="stat-num"><?= $diasAusencia ?></div>
          <div class="stat-label">Días de ausencia aprobados</div>
        </div>
      </div>
    </div>

    <?php if ($rol === 'finanzas'): ?>
    <!-- KPIs de Finanzas -->
    <div class="stats-grid" style="margin-bottom:1.75rem">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,.12);color:#f87171">💰</div>
        <div class="stat-body">
          <div class="stat-num">$<?= number_format($totalNomina,0,',','.') ?></div>
          <div class="stat-label">Nómina mensual total</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#fbbf24">📉</div>
        <div class="stat-body">
          <div class="stat-num">$<?= number_format($costoAusencia,0,',','.') ?></div>
          <div class="stat-label">Costo estimado ausencias</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.12);color:#60a5fa">👥</div>
        <div class="stat-body">
          <div class="stat-num">$<?= number_format($countEmp > 0 ? $totalNomina/$countEmp : 0,0,',','.') ?></div>
          <div class="stat-label">Sueldo promedio</div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="report-grid">

      <!-- Por tipo -->
      <div class="panel">
        <div class="panel-header"><h2>Solicitudes por tipo</h2></div>
        <div class="bar-chart">
          <?php
          $colors = ['vacaciones'=>'#3b82f6','franco'=>'#06b6d4','permiso'=>'#f59e0b','certificado'=>'#8b5cf6','licencia'=>'#ef4444'];
          $maxTipo = max(array_values($porTipo)) ?: 1;
          foreach ($porTipo as $k => $n):
            $t = $TIPOS_SOLICITUD[$k];
            $pct = round($n / $maxTipo * 100);
            $col = $colors[$k] ?? '#3b82f6';
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= $t['icon'].' '.$t['label'] ?></span>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
            <span class="bar-val"><?= $n ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Por estado -->
      <div class="panel">
        <div class="panel-header"><h2>Solicitudes por estado</h2></div>
        <div class="bar-chart">
          <?php
          $stateColors = ['pendiente_lider'=>'#f59e0b','pendiente_rrhh'=>'#3b82f6','aprobada'=>'#22c55e','rechazada'=>'#ef4444','cancelada'=>'#6b7280'];
          $maxEst = max(array_values($porEstado)) ?: 1;
          foreach ($porEstado as $k => $n):
            $e = $ESTADOS[$k];
            $pct = round($n / $maxEst * 100);
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= htmlspecialchars($e['label']) ?></span>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $stateColors[$k] ?? '#888' ?>"></div></div>
            <span class="bar-val"><?= $n ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Canal de origen -->
      <div class="panel">
        <div class="panel-header"><h2>Canal de origen</h2></div>
        <div class="donut-wrap">
          <?php
          $wPct = $total > 0 ? round($whatsapp/$total*100) : 0;
          $pPct = $total > 0 ? round($portal/$total*100)   : 0;
          $r = 50; $circ = 2 * M_PI * $r;
          $wDash = $circ * $wPct / 100;
          ?>
          <svg width="130" height="130" viewBox="0 0 130 130">
            <circle cx="65" cy="65" r="<?= $r ?>" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="14"/>
            <circle cx="65" cy="65" r="<?= $r ?>" fill="none" stroke="#22c55e" stroke-width="14"
              stroke-dasharray="<?= round($wDash,1) ?> <?= round($circ - $wDash,1) ?>"
              stroke-dashoffset="<?= round($circ/4,1) ?>" stroke-linecap="round"/>
            <text x="65" y="60" text-anchor="middle" fill="white" font-size="18" font-weight="800"><?= $wPct ?>%</text>
            <text x="65" y="78" text-anchor="middle" fill="#94a3b8" font-size="9">WhatsApp</text>
          </svg>
          <div class="legend">
            <div class="legend-item"><div class="legend-dot" style="background:#22c55e"></div><span>📱 WhatsApp: <?= $whatsapp ?> (<?= $wPct ?>%)</span></div>
            <div class="legend-item"><div class="legend-dot" style="background:#3b82f6"></div><span>🌐 Portal: <?= $portal ?> (<?= $pPct ?>%)</span></div>
            <div style="font-size:.78rem;color:var(--gray);margin-top:.5rem">Total: <?= $total ?> solicitudes</div>
          </div>
        </div>
      </div>

      <!-- Nómina por área (solo rrhh/finanzas) -->
      <?php if (in_array($rol, ['rrhh','finanzas'])): ?>
      <div class="panel">
        <div class="panel-header"><h2>Nómina por área</h2></div>
        <?php
        $porArea = [];
        foreach ($EMPLEADOS as $e) {
            $a = $e['area'];
            if (!isset($porArea[$a])) $porArea[$a] = ['total' => 0, 'count' => 0];
            $porArea[$a]['total'] += $e['sueldo'];
            $porArea[$a]['count']++;
        }
        arsort($porArea);
        $maxArea = max(array_column($porArea,'total')) ?: 1;
        $aColors = ['#3b82f6','#06b6d4','#8b5cf6','#f59e0b','#ef4444'];
        $ai = 0;
        ?>
        <div class="bar-chart">
          <?php foreach ($porArea as $area => $data):
            $pct = round($data['total'] / $maxArea * 100);
            $col = $aColors[$ai++ % count($aColors)];
          ?>
          <div class="bar-item">
            <span class="bar-label" style="font-size:.75rem"><?= htmlspecialchars($area) ?></span>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
            <span class="bar-val" style="width:80px;font-size:.75rem">$<?= number_format($data['total'],0,',','.') ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- Tabla detallada -->
    <div class="panel" style="margin-top:1.25rem">
      <div class="panel-header"><h2>Detalle de empleados</h2></div>
      <table class="data-table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Área</th>
            <th>Puesto</th>
            <?php if (in_array($rol, ['rrhh','finanzas'])): ?><th>Sueldo</th><?php endif; ?>
            <th>Vac. disp.</th>
            <th>Francos disp.</th>
            <th>Solicitudes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($EMPLEADOS as $emp):
            $empCount = count(array_filter($SOLICITUDES, fn($s) => $s['empleado_id'] === $emp['id']));
            $vacLib = $emp['dias_vacaciones_anuales'] - $emp['dias_vacaciones_usados'];
            $fraLib = $emp['dias_franco_disponibles'] - $emp['dias_franco_usados'];
          ?>
          <tr>
            <td><a href="empleados.php?id=<?= $emp['id'] ?>" class="tbl-link"><?= htmlspecialchars($emp['nombre']) ?></a></td>
            <td><?= htmlspecialchars($emp['area']) ?></td>
            <td><?= htmlspecialchars($emp['puesto']) ?></td>
            <?php if (in_array($rol, ['rrhh','finanzas'])): ?>
              <td>$<?= number_format($emp['sueldo'],0,',','.') ?></td>
            <?php endif; ?>
            <td><?= $vacLib ?> / <?= $emp['dias_vacaciones_anuales'] ?></td>
            <td><?= $fraLib ?></td>
            <td><?= $empCount ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>
<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
</body>
</html>
