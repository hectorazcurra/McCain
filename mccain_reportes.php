<?php
// ══════════════════════════════════════════════════
//  MAYA · Reportes McCain — Analíticas y métricas
// ══════════════════════════════════════════════════

require_once __DIR__ . '/mccain_config.php';
mcRequireLogin();
$user = mcCurrentUser();

// ── Cálculos ─────────────────────────────────────
$totalConsultas   = count($MCCAIN_CONSULTAS);
$totalVendedores  = count($MCCAIN_VENDEDORES);
$vendActivos      = count(array_filter($MCCAIN_VENDEDORES, fn($v) => $v['estado'] === 'activo'));
$hoy              = date('Y-m-d');

// Por tipo
$porTipo = [];
foreach ($MCCAIN_CONSULTAS as $c) {
    $t = $c['tipo_consulta'];
    $porTipo[$t] = ($porTipo[$t] ?? 0) + 1;
}
arsort($porTipo);
$maxTipo = max(array_values($porTipo)) ?: 1;

// Por región
$porRegion = [];
foreach ($MCCAIN_VENDEDORES as $v) {
    $r = $v['region'];
    $porRegion[$r] = ($porRegion[$r] ?? 0) + 1;
}
arsort($porRegion);
$maxRegion = max(array_values($porRegion)) ?: 1;

// Consultas últimos 14 días
$porDia14 = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $porDia14[$d] = 0;
}
foreach ($MCCAIN_CONSULTAS as $c) {
    $d = substr($c['created_at'], 0, 10);
    if (isset($porDia14[$d])) $porDia14[$d]++;
}
$maxDia = max(array_values($porDia14)) ?: 1;

// Top vendedores por actividad
$porVendedor = [];
foreach ($MCCAIN_CONSULTAS as $c) {
    if ($c['vendedor_id']) {
        $porVendedor[$c['vendedor_id']] = ($porVendedor[$c['vendedor_id']] ?? 0) + 1;
    }
}
arsort($porVendedor);
$topVendedores = array_slice($porVendedor, 0, 8, true);
$maxVend = $topVendedores ? max(array_values($topVendedores)) : 1;

// Usuarios únicos
$numUnicos = count(array_unique(array_column($MCCAIN_CONSULTAS, 'numero_whatsapp')));

// Avg diario (últimos 30 días)
$avgDiario = round($totalConsultas / 35, 1);

// Crecimiento de registros por semana (últimas 6 semanas)
$porSemana = [];
for ($i = 5; $i >= 0; $i--) {
    $start = date('Y-m-d', strtotime("-" . ($i * 7 + 6) . " days"));
    $end   = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
    $label = 'S' . (6 - $i);
    $porSemana[$label] = 0;
    foreach ($MCCAIN_VENDEDORES as $v) {
        $d = substr($v['created_at'], 0, 10);
        if ($d >= $start && $d <= $end) $porSemana[$label]++;
    }
}
$maxSemana = max(array_values($porSemana)) ?: 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAYA · Reportes McCain</title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
<style>
/* ── McCain brand override ── */
:root {
  --blue:   #C8102E;
  --blue2:  #a50d26;
  --blue3:  #e8405a;
  --navy:   #1a0005;
  --navy2:  #2d000a;
  --navy3:  #400010;
  --border: rgba(200,16,46,.18);
}
.sidebar a.active {
  color: #e8405a;
  border-left: 3px solid #C8102E;
  background: rgba(200,16,46,.1);
}

/* ── Charts ── */
.report-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:1.25rem; }
.bar-chart   { display:flex; flex-direction:column; gap:.65rem; margin-top:.75rem; }
.bar-item    { display:flex; align-items:center; gap:.75rem; font-size:.82rem; }
.bar-label   { width:130px; flex-shrink:0; color:var(--gray); font-size:.78rem; }
.bar-track   { flex:1; height:8px; background:rgba(255,255,255,.07); border-radius:4px; }
.bar-fill    { height:100%; border-radius:4px; transition:width .4s; }
.bar-val     { width:32px; text-align:right; font-weight:600; }

/* Day bars (column chart) */
.col-chart   { display:flex; align-items:flex-end; gap:.28rem; height:80px; margin-top:.75rem; }
.col-bar     { flex:1; border-radius:3px 3px 0 0; background:#C8102E; opacity:.75; min-height:3px; position:relative; }
.col-bar:hover { opacity:1; }
.col-bar .tip {
  display:none; position:absolute; bottom:calc(100%+4px); left:50%; transform:translateX(-50%);
  background:#1a0005; border:1px solid rgba(200,16,46,.3); border-radius:5px;
  padding:.2rem .4rem; font-size:.66rem; color:#e8d8da; white-space:nowrap; z-index:10;
}
.col-bar:hover .tip { display:block; }
.col-labels  { display:flex; gap:.28rem; margin-top:.25rem; }
.col-labels span { flex:1; text-align:center; font-size:.56rem; color:var(--gray); }

/* Donut */
.donut-wrap  { display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; margin-top:.75rem; }
.legend      { display:flex; flex-direction:column; gap:.4rem; }
.legend-item { display:flex; align-items:center; gap:.5rem; font-size:.8rem; }
.legend-dot  { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
</style>
</head>
<body>

<!-- Nav -->
<nav class="nav">
  <div class="nav-brand">
    <span style="font-weight:900;color:#fff">Mc<span style="color:#e8405a">CAIN</span></span>
    <span style="font-size:.75rem;color:var(--gray);margin-left:.5rem">MAYA · Portal</span>
  </div>
  <div class="nav-user">
    <span><?= htmlspecialchars($user['nombre']) ?></span>
    <a href="mccain_logout.php" style="color:var(--gray);font-size:.8rem;margin-left:1rem;text-decoration:none">Salir →</a>
  </div>
</nav>

<div class="layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <nav>
      <a href="mccain_dashboard.php" id="nav-dashboard">📊 Dashboard</a>
      <a href="mccain_reportes.php"  id="nav-reportes">📈 Reportes</a>
    </nav>
  </aside>

  <main class="main">
    <div class="page-header">
      <div>
        <h1>Reportes MAYA</h1>
        <p class="page-sub">Métricas de uso del chatbot · Datos demo · <?= date('d/m/Y') ?></p>
      </div>
    </div>

    <!-- KPIs -->
    <div class="stats-grid" style="margin-bottom:1.75rem">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(200,16,46,.15);color:#e8405a">💬</div>
        <div class="stat-body">
          <div class="stat-num"><?= $totalConsultas ?></div>
          <div class="stat-label">Consultas totales</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);color:#4ade80">👥</div>
        <div class="stat-body">
          <div class="stat-num"><?= $numUnicos ?></div>
          <div class="stat-label">Usuarios únicos</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);color:#60a5fa">📊</div>
        <div class="stat-body">
          <div class="stat-num"><?= $avgDiario ?></div>
          <div class="stat-label">Promedio consultas/día</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(6,182,212,.15);color:#67e8f9">👤</div>
        <div class="stat-body">
          <div class="stat-num"><?= $vendActivos ?> / <?= $totalVendedores ?></div>
          <div class="stat-label">Vendedores activos</div>
        </div>
      </div>
    </div>

    <div class="report-grid">

      <!-- Consultas por tipo -->
      <div class="panel">
        <div class="panel-header"><h2>Consultas por tipo</h2></div>
        <div class="bar-chart">
          <?php foreach ($porTipo as $tipo => $cnt):
            $pct = round($cnt / $maxTipo * 100);
            $col = mcTipoColor($tipo);
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= mcTipoLabel($tipo) ?></span>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
            <span class="bar-val"><?= $cnt ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Consultas últimos 14 días -->
      <div class="panel">
        <div class="panel-header"><h2>Actividad últimos 14 días</h2></div>
        <div class="col-chart">
          <?php foreach ($porDia14 as $fecha => $cnt):
            $h = $maxDia > 0 ? max(3, round($cnt / $maxDia * 80)) : 3;
            $dm = date('d/m', strtotime($fecha));
          ?>
            <div class="col-bar" style="height:<?= $h ?>px">
              <div class="tip"><?= $dm ?>: <?= $cnt ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="col-labels">
          <?php foreach ($porDia14 as $fecha => $cnt): ?>
            <span><?= date('d', strtotime($fecha)) ?></span>
          <?php endforeach; ?>
        </div>
        <div style="font-size:.72rem;color:var(--gray);margin-top:.5rem;text-align:center">
          Total período: <?= array_sum($porDia14) ?> consultas
        </div>
      </div>

      <!-- Canal de origen (100% WhatsApp) -->
      <div class="panel">
        <div class="panel-header"><h2>Canal de origen</h2></div>
        <div class="donut-wrap">
          <?php
          $r = 50; $circ = 2 * M_PI * $r;
          $wDash = $circ; // 100%
          ?>
          <svg width="130" height="130" viewBox="0 0 130 130">
            <circle cx="65" cy="65" r="<?= $r ?>" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="14"/>
            <circle cx="65" cy="65" r="<?= $r ?>" fill="none" stroke="#22c55e" stroke-width="14"
              stroke-dasharray="<?= round($wDash,1) ?> 0"
              stroke-dashoffset="<?= round($circ/4,1) ?>" stroke-linecap="round"/>
            <text x="65" y="60" text-anchor="middle" fill="white" font-size="18" font-weight="800">100%</text>
            <text x="65" y="78" text-anchor="middle" fill="#94a3b8" font-size="9">WhatsApp</text>
          </svg>
          <div class="legend">
            <div class="legend-item"><div class="legend-dot" style="background:#22c55e"></div><span>📱 WhatsApp: <?= $totalConsultas ?> (100%)</span></div>
            <div style="font-size:.75rem;color:var(--gray);margin-top:.5rem">
              MAYA opera 100% vía<br>WhatsApp con Evolution API
            </div>
          </div>
        </div>
      </div>

      <!-- Vendedores por región -->
      <div class="panel">
        <div class="panel-header"><h2>Vendedores por región</h2></div>
        <div class="bar-chart">
          <?php
          $rColors = ['#C8102E','#e07b00','#3b82f6','#8b5cf6','#22c55e','#06b6d4','#f59e0b'];
          $ri = 0;
          foreach ($porRegion as $region => $cnt):
            $pct = round($cnt / $maxRegion * 100);
            $col = $rColors[$ri++ % count($rColors)];
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= htmlspecialchars($region) ?></span>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
            <span class="bar-val"><?= $cnt ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /report-grid -->

    <!-- Top vendedores por actividad -->
    <div class="panel" style="margin-top:1.25rem">
      <div class="panel-header"><h2>Top vendedores por actividad</h2></div>
      <div class="bar-chart">
        <?php foreach ($topVendedores as $vid => $cnt):
          $pct = round($cnt / $maxVend * 100);
          $nombre = mcVendedorNombre($vid);
        ?>
        <div class="bar-item">
          <span class="bar-label"><?= htmlspecialchars($nombre) ?></span>
          <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%;background:#C8102E"></div></div>
          <span class="bar-val"><?= $cnt ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Crecimiento de registros por semana -->
    <div class="panel" style="margin-top:1.25rem">
      <div class="panel-header">
        <h2>Registros de vendedores por semana</h2>
        <span style="font-size:.75rem;color:var(--gray)">Últimas 6 semanas</span>
      </div>
      <div style="display:flex;align-items:flex-end;gap:.75rem;height:80px;margin-top:.75rem">
        <?php foreach ($porSemana as $label => $cnt):
          $h = $maxSemana > 0 ? max(3, round($cnt / $maxSemana * 80)) : 3;
        ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.3rem">
          <span style="font-size:.72rem;font-weight:700;color:var(--text)"><?= $cnt ?></span>
          <div style="width:100%;height:<?= $h ?>px;background:#C8102E;border-radius:4px 4px 0 0;opacity:.8"></div>
          <span style="font-size:.68rem;color:var(--gray)"><?= $label ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Tabla detallada de vendedores -->
    <div class="panel" style="margin-top:1.25rem">
      <div class="panel-header"><h2>Detalle de vendedores</h2></div>
      <table class="data-table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>WhatsApp</th>
            <th>Región</th>
            <th>Estado</th>
            <th>Consultas</th>
            <th>Registrado</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($MCCAIN_VENDEDORES as $v):
            $consultas = count(array_filter($MCCAIN_CONSULTAS, fn($c) => $c['vendedor_id'] === $v['id']));
          ?>
          <tr>
            <td><?= htmlspecialchars($v['nombre']) ?></td>
            <td style="font-family:monospace;font-size:.78rem;color:var(--gray)"><?= htmlspecialchars($v['numero_whatsapp']) ?></td>
            <td><?= htmlspecialchars($v['region']) ?></td>
            <td>
              <?php if ($v['estado'] === 'activo'): ?>
                <span class="badge" style="background:rgba(34,197,94,.12);color:#4ade80">Activo</span>
              <?php else: ?>
                <span class="badge" style="background:rgba(107,114,128,.12);color:#9ca3af">Inactivo</span>
              <?php endif; ?>
            </td>
            <td style="font-weight:700;color:<?= $consultas > 3 ? '#e8405a' : 'inherit' ?>"><?= $consultas ?></td>
            <td style="font-size:.78rem;color:var(--gray)"><?= mcFormatDate($v['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>

<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
<script>
document.querySelectorAll('.sidebar a').forEach(a => {
  if (a.href.includes('reportes')) a.classList.add('active');
});
</script>
</body>
</html>
