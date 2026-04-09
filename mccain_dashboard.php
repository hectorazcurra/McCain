<?php
// ══════════════════════════════════════════════════
//  MAYA · Dashboard McCain — Monitor de consultas
// ══════════════════════════════════════════════════

require_once __DIR__ . '/mccain_config.php';
mcRequireLogin();
$user = mcCurrentUser();

// ── Stats ────────────────────────────────────────
$hoy              = date('Y-m-d');
$inicioMes        = date('Y-m-01');
$totalConsultas   = count($MCCAIN_CONSULTAS);
$totalVendedores  = count($MCCAIN_VENDEDORES);
$vendActivos      = count(array_filter($MCCAIN_VENDEDORES, fn($v) => $v['estado'] === 'activo'));
$consultasHoy     = count(array_filter($MCCAIN_CONSULTAS, fn($c) => substr($c['created_at'],0,10) === $hoy));
$nuevosMes        = count(array_filter($MCCAIN_VENDEDORES, fn($v) => substr($v['created_at'],0,7) === date('Y-m')));

// ── Top tipos ────────────────────────────────────
$porTipo = [];
foreach ($MCCAIN_CONSULTAS as $c) {
    $t = $c['tipo_consulta'];
    $porTipo[$t] = ($porTipo[$t] ?? 0) + 1;
}
arsort($porTipo);
$topTipos  = array_slice($porTipo, 0, 6, true);
$totalLogs = count($MCCAIN_CONSULTAS);

// ── Ultimas interacciones ─────────────────────────
$recientes = $MCCAIN_CONSULTAS;
usort($recientes, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
$recientes = array_slice($recientes, 0, 8);

// ── Por región ───────────────────────────────────
$porRegion = [];
foreach ($MCCAIN_VENDEDORES as $v) {
    $r = $v['region'];
    $porRegion[$r] = ($porRegion[$r] ?? 0) + 1;
}
arsort($porRegion);
$maxRegion = max(array_values($porRegion)) ?: 1;

// ── Consultas últimos 7 días ──────────────────────
$porDia7 = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $porDia7[$d] = 0;
}
foreach ($MCCAIN_CONSULTAS as $c) {
    $d = substr($c['created_at'],0,10);
    if (isset($porDia7[$d])) $porDia7[$d]++;
}
$maxDia7 = max(array_values($porDia7)) ?: 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAYA · Dashboard McCain</title>
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
.btn-primary { background: #C8102E; }
.btn-primary:hover { background: #a50d26; }

/* ── Mini day-bar chart ── */
.day-bars { display:flex; align-items:flex-end; gap:.35rem; height:52px; margin-top:.75rem; }
.day-bar  { flex:1; border-radius:4px 4px 0 0; background:#C8102E; opacity:.8; min-height:3px; transition:opacity .2s; position:relative; }
.day-bar:hover { opacity:1; }
.day-bar .tip {
  display:none; position:absolute; bottom:calc(100% + 4px); left:50%; transform:translateX(-50%);
  background:#1a0005; border:1px solid rgba(200,16,46,.3); border-radius:6px;
  padding:.2rem .45rem; font-size:.68rem; color:#e8d8da; white-space:nowrap;
}
.day-bar:hover .tip { display:block; }
.day-labels { display:flex; gap:.35rem; margin-top:.3rem; }
.day-labels span { flex:1; text-align:center; font-size:.58rem; color:var(--gray); }

/* ── Región bars ── */
.region-bar-item { display:flex; align-items:center; gap:.75rem; font-size:.82rem; margin-bottom:.55rem; }
.region-label    { width:110px; flex-shrink:0; color:var(--gray); font-size:.78rem; }
.region-track    { flex:1; height:8px; background:rgba(255,255,255,.07); border-radius:4px; }
.region-fill     { height:100%; border-radius:4px; background:#C8102E; }
.region-val      { width:22px; text-align:right; font-weight:700; font-size:.8rem; }

/* ── Interaction feed ── */
.feed-item {
  display:flex; align-items:flex-start; gap:.75rem;
  padding:.6rem 0; border-bottom:1px solid rgba(255,255,255,.04);
}
.feed-item:last-child { border-bottom:none; }
.feed-icon { font-size:1.1rem; flex-shrink:0; margin-top:.05rem; }
.feed-body { flex:1; min-width:0; }
.feed-name { font-size:.8rem; font-weight:600; }
.feed-msg  { font-size:.77rem; color:var(--gray); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.feed-meta { text-align:right; flex-shrink:0; }
.feed-time { font-size:.7rem; color:var(--gray); }
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
        <h1>Dashboard MAYA</h1>
        <p class="page-sub">Monitor de consultas de vendedores · Datos demo · <?= date('d/m/Y') ?></p>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(200,16,46,.15);color:#e8405a">💬</div>
        <div class="stat-body">
          <div class="stat-num"><?= $totalConsultas ?></div>
          <div class="stat-label">Consultas totales</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);color:#4ade80">👤</div>
        <div class="stat-body">
          <div class="stat-num"><?= $vendActivos ?></div>
          <div class="stat-label">Vendedores activos</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);color:#fbbf24">📅</div>
        <div class="stat-body">
          <div class="stat-num"><?= $consultasHoy ?></div>
          <div class="stat-label">Consultas hoy</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(6,182,212,.15);color:#67e8f9">🆕</div>
        <div class="stat-body">
          <div class="stat-num"><?= $nuevosMes ?></div>
          <div class="stat-label">Nuevos este mes</div>
        </div>
      </div>
    </div>

    <!-- Section grid: Top temas + Feed -->
    <div class="section-grid">

      <!-- Panel: Top temas -->
      <div class="panel">
        <div class="panel-header">
          <h2>Top temas consultados</h2>
          <span style="font-size:.75rem;color:var(--gray)">Últimas interacciones</span>
        </div>

        <!-- Mini chart: consultas últimos 7 días -->
        <div style="font-size:.72rem;font-weight:600;color:var(--gray);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.4rem">
          Actividad últimos 7 días
        </div>
        <div class="day-bars">
          <?php foreach ($porDia7 as $fecha => $cnt):
            $h = $maxDia7 > 0 ? max(6, round($cnt / $maxDia7 * 52)) : 6;
            $dm = date('d/m', strtotime($fecha));
          ?>
            <div class="day-bar" style="height:<?= $h ?>px">
              <div class="tip"><?= $dm ?>: <?= $cnt ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="day-labels">
          <?php foreach ($porDia7 as $fecha => $cnt): ?>
            <span><?= date('d', strtotime($fecha)) ?></span>
          <?php endforeach; ?>
        </div>

        <!-- Bar list: top tipos -->
        <div style="font-size:.72rem;font-weight:600;color:var(--gray);text-transform:uppercase;letter-spacing:.08em;margin:1.1rem 0 .6rem">
          Por tipo de consulta
        </div>
        <div style="display:flex;flex-direction:column;gap:.5rem">
          <?php foreach ($topTipos as $tipo => $cnt):
            $pct = $totalLogs > 0 ? round($cnt / $totalLogs * 100) : 0;
            $col = mcTipoColor($tipo);
          ?>
          <div style="display:flex;align-items:center;gap:.6rem;font-size:.8rem">
            <span style="width:130px;color:var(--gray)"><?= mcTipoLabel($tipo) ?></span>
            <div style="flex:1;height:6px;background:rgba(255,255,255,.07);border-radius:3px">
              <div style="height:100%;width:<?= $pct ?>%;background:<?= $col ?>;border-radius:3px"></div>
            </div>
            <span style="width:28px;text-align:right;font-weight:700"><?= $cnt ?></span>
            <span style="color:var(--gray);width:32px"><?= $pct ?>%</span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Panel: Ultimas interacciones -->
      <div class="panel">
        <div class="panel-header">
          <h2>Últimas interacciones</h2>
          <span style="font-size:.75rem;color:var(--gray)">Conversaciones recientes con MAYA</span>
        </div>
        <div>
          <?php foreach ($recientes as $log):
            $icono = mb_substr(mcTipoLabel($log['tipo_consulta']), 0, 2);
            $nombre = $log['vendedor_id'] ? mcVendedorNombre($log['vendedor_id']) : 'Sin registrar';
          ?>
          <div class="feed-item">
            <span class="feed-icon"><?= $icono ?></span>
            <div class="feed-body">
              <div class="feed-name"><?= htmlspecialchars($nombre) ?></div>
              <div class="feed-msg"><?= htmlspecialchars(mb_strimwidth($log['mensaje_usuario'], 0, 55, '…')) ?></div>
            </div>
            <div class="feed-meta">
              <div class="feed-time"><?= mcFormatDateTime($log['created_at']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /section-grid -->

    <!-- Panel: Distribución por región -->
    <div class="panel" style="margin-top:0">
      <div class="panel-header">
        <h2>Vendedores por región</h2>
        <span style="font-size:.75rem;color:var(--gray)"><?= $totalVendedores ?> vendedores registrados</span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div>
          <?php foreach ($porRegion as $region => $cnt):
            $pct = round($cnt / $maxRegion * 100);
          ?>
          <div class="region-bar-item">
            <span class="region-label"><?= htmlspecialchars($region) ?></span>
            <div class="region-track"><div class="region-fill" style="width:<?= $pct ?>%"></div></div>
            <span class="region-val"><?= $cnt ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div>
          <table class="data-table" style="margin-top:0">
            <thead><tr><th>Vendedor</th><th>Región</th><th>Estado</th></tr></thead>
            <tbody>
            <?php foreach (array_slice($MCCAIN_VENDEDORES, 0, 8, true) as $v): ?>
              <tr>
                <td><?= htmlspecialchars($v['nombre']) ?></td>
                <td style="font-size:.78rem;color:var(--gray)"><?= htmlspecialchars($v['region']) ?></td>
                <td>
                  <?php if ($v['estado'] === 'activo'): ?>
                    <span class="badge" style="background:rgba(34,197,94,.12);color:#4ade80">Activo</span>
                  <?php else: ?>
                    <span class="badge" style="background:rgba(107,114,128,.12);color:#9ca3af">Inactivo</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </main>
</div>

<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
<script>
// Activar link del sidebar
document.querySelectorAll('.sidebar a').forEach(a => {
  if (a.href.includes('dashboard')) a.classList.add('active');
});
</script>
</body>
</html>
