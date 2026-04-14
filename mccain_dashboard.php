<?php
// ══════════════════════════════════════════════════
//  MAYA · Dashboard McCain — Monitor de consultas
// ══════════════════════════════════════════════════

require_once __DIR__ . '/mccain_config.php';
mcRequireLogin();
$user = mcCurrentUser();

// ── Stats ────────────────────────────────────────
$hoy             = date('Y-m-d');
$totalConsultas  = count($MCCAIN_CONSULTAS);
$totalVendedores = count($MCCAIN_VENDEDORES);
$vendActivos     = count(array_filter($MCCAIN_VENDEDORES, fn($v) => $v['estado'] === 'activo'));
$consultasHoy    = count(array_filter($MCCAIN_CONSULTAS, fn($c) => substr($c['created_at'],0,10) === $hoy));
$nuevosMes       = count(array_filter($MCCAIN_VENDEDORES, fn($v) => substr($v['created_at'],0,7) === date('Y-m')));

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

// ── Actividad últimos 7 días ─────────────────────
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
<title>MAYA · Dashboard — McCain</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════
   MAYA McCain — Design System Light
   Paleta: #C8102E (rojo) + #FFC72C (amarillo) + blanco/gris claro
   ══════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --red:       #C8102E;
  --red-d:     #A00020;
  --red-l:     #FCEEF1;
  --red-m:     rgba(200,16,46,.10);
  --yellow:    #FFC72C;
  --yellow-d:  #E6A800;
  --yellow-l:  #FFFBF0;
  --yellow-m:  rgba(255,199,44,.15);
  --green:     #16A34A;
  --green-l:   #F0FDF4;
  --green-m:   rgba(22,163,74,.12);
  --blue:      #2563EB;
  --blue-l:    #EFF6FF;
  --blue-m:    rgba(37,99,235,.10);
  --bg:        #F4F2EE;
  --bg2:       #EDEAE4;
  --white:     #FFFFFF;
  --text:      #1A1A1A;
  --text2:     #5C5C5C;
  --gray:      #9A9A9A;
  --border:    #DDD8D0;
  --border2:   #EAE6E0;
  --shadow-sm: 0 1px 4px rgba(0,0,0,.06);
  --shadow:    0 2px 12px rgba(0,0,0,.08);
  --shadow-md: 0 4px 24px rgba(0,0,0,.10);
  --radius:    14px;
  --radius-sm: 9px;
  --sidebar-w: 230px;
}

html, body {
  background: var(--bg);
  color: var(--text);
  font-family: 'Inter', system-ui, sans-serif;
  font-size: .9rem;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

/* ══ TOP NAV ══ */
.mc-nav {
  position: sticky; top: 0; z-index: 100;
  background: var(--red);
  height: 60px;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 1.5rem;
  box-shadow: 0 2px 8px rgba(200,16,46,.3);
}
.mc-nav-left  { display: flex; align-items: center; gap: 1rem; }
.mc-nav-logo img {
  height: 36px; object-fit: contain;
  filter: brightness(0) invert(1);
}
.mc-nav-logo .logo-text {
  font-size: 1.4rem; font-weight: 900; letter-spacing: -.03em; color: #fff;
}
.mc-nav-logo .logo-text span { color: var(--yellow); }
.mc-nav-divider {
  width: 1px; height: 28px; background: rgba(255,255,255,.3); margin: 0 .5rem;
}
.mc-nav-title {
  font-size: .8rem; font-weight: 600; color: rgba(255,255,255,.85);
  letter-spacing: .04em; text-transform: uppercase;
}
.mc-nav-right { display: flex; align-items: center; gap: 1rem; }
.mc-nav-user  { font-size: .82rem; color: rgba(255,255,255,.8); }
.mc-nav-user strong { color: #fff; font-weight: 600; }
.mc-nav-logout {
  font-size: .8rem; font-weight: 600; color: #fff;
  text-decoration: none; background: rgba(255,255,255,.15);
  padding: .35rem .8rem; border-radius: 7px;
  transition: background .15s;
}
.mc-nav-logout:hover { background: rgba(255,255,255,.25); }

/* ══ LAYOUT ══ */
.mc-layout { display: flex; min-height: calc(100vh - 60px); }

/* ══ SIDEBAR ══ */
.mc-sidebar {
  width: var(--sidebar-w); flex-shrink: 0;
  background: var(--white);
  border-right: 1px solid var(--border);
  padding: 1.5rem 0;
  position: sticky; top: 60px; height: calc(100vh - 60px); overflow-y: auto;
}
.mc-sidebar-section { margin-bottom: 1.75rem; }
.mc-sidebar-label {
  font-size: .65rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .14em; color: var(--gray);
  padding: 0 1.1rem .6rem;
}
.mc-sidebar a {
  display: flex; align-items: center; gap: .65rem;
  padding: .6rem 1.1rem; color: var(--text2);
  text-decoration: none; font-size: .875rem; font-weight: 500;
  border-left: 3px solid transparent;
  transition: all .15s; margin-bottom: .1rem;
}
.mc-sidebar a:hover { color: var(--red); background: var(--red-l); }
.mc-sidebar a.active {
  color: var(--red); border-left-color: var(--red);
  background: var(--red-l); font-weight: 600;
}
.mc-sidebar .s-icon { font-size: .95rem; width: 20px; text-align: center; }

/* ══ MAIN ══ */
.mc-main { flex: 1; padding: 2rem 1.75rem; min-width: 0; }

.mc-page-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;
}
.mc-page-header h1 {
  font-size: 1.45rem; font-weight: 800; color: var(--text);
  display: flex; align-items: center; gap: .6rem;
}
.mc-page-header h1 .h-badge {
  font-size: .7rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; color: var(--red);
  background: var(--red-l); padding: .2rem .6rem; border-radius: 5px;
}
.mc-page-sub { color: var(--text2); font-size: .83rem; margin-top: .2rem; }

/* ══ STAT CARDS ══ */
.mc-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.75rem; }
.mc-stat {
  background: var(--white); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 1.25rem 1.25rem 1.1rem;
  box-shadow: var(--shadow-sm);
  display: flex; align-items: flex-start; gap: 1rem;
  transition: box-shadow .2s;
  position: relative; overflow: hidden;
}
.mc-stat::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: var(--stat-color, var(--red));
}
.mc-stat:hover { box-shadow: var(--shadow); }
.mc-stat-icon {
  width: 46px; height: 46px; border-radius: 11px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
  background: var(--stat-bg, var(--red-l));
}
.mc-stat-body {}
.mc-stat-num   { font-size: 1.8rem; font-weight: 800; color: var(--text); line-height: 1; }
.mc-stat-label { font-size: .74rem; color: var(--text2); margin-top: .25rem; font-weight: 500; }

/* ══ SECTION GRID ══ */
.mc-section { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
.mc-section.wide { grid-template-columns: 1fr; }

/* ══ CARD ══ */
.mc-card {
  background: var(--white); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 1.4rem;
  box-shadow: var(--shadow-sm);
}
.mc-card-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.1rem;
}
.mc-card-header h2 { font-size: .95rem; font-weight: 700; color: var(--text); }
.mc-card-header .mc-badge-sm {
  font-size: .72rem; font-weight: 600; color: var(--text2);
  background: var(--bg); padding: .25rem .65rem; border-radius: 99px;
  border: 1px solid var(--border);
}

/* ══ TIPO BARS ══ */
.tipo-bar { display: flex; align-items: center; gap: .75rem; margin-bottom: .55rem; }
.tipo-label { width: 130px; flex-shrink: 0; font-size: .78rem; color: var(--text2); }
.tipo-track { flex: 1; height: 7px; background: var(--bg2); border-radius: 4px; overflow: hidden; }
.tipo-fill  { height: 100%; border-radius: 4px; }
.tipo-val   { width: 26px; text-align: right; font-size: .8rem; font-weight: 700; color: var(--text); }
.tipo-pct   { width: 36px; font-size: .75rem; color: var(--gray); }

/* ══ ACTIVITY BARS (column chart) ══ */
.act-wrap  { margin-top: .75rem; }
.act-bars  { display: flex; align-items: flex-end; gap: .3rem; height: 64px; }
.act-bar   {
  flex: 1; border-radius: 4px 4px 0 0; background: var(--red);
  opacity: .75; min-height: 3px; cursor: pointer; position: relative;
  transition: opacity .15s;
}
.act-bar:hover { opacity: 1; }
.act-bar .tip {
  display: none; position: absolute; bottom: calc(100% + 5px); left: 50%;
  transform: translateX(-50%); background: var(--text); color: #fff;
  font-size: .68rem; padding: .2rem .45rem; border-radius: 5px; white-space: nowrap; z-index: 10;
}
.act-bar:hover .tip { display: block; }
.act-labels { display: flex; gap: .3rem; margin-top: .3rem; }
.act-labels span { flex: 1; text-align: center; font-size: .58rem; color: var(--gray); }

/* ══ FEED (interacciones) ══ */
.feed-item {
  display: flex; align-items: flex-start; gap: .8rem;
  padding: .7rem 0; border-bottom: 1px solid var(--border2);
}
.feed-item:last-child { border-bottom: none; }
.feed-avatar {
  width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
  background: var(--red-m); display: flex; align-items: center; justify-content: center;
  font-size: .85rem; font-weight: 700; color: var(--red);
}
.feed-body { flex: 1; min-width: 0; }
.feed-name { font-size: .82rem; font-weight: 600; color: var(--text); }
.feed-msg  { font-size: .78rem; color: var(--text2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: .05rem; }
.feed-meta { flex-shrink: 0; text-align: right; }
.feed-time { font-size: .72rem; color: var(--gray); }
.feed-tipo {
  display: inline-block; margin-top: .2rem; font-size: .68rem; font-weight: 600;
  padding: .1rem .5rem; border-radius: 99px;
  background: var(--yellow-l); color: var(--yellow-d);
  border: 1px solid rgba(255,199,44,.4);
}

/* ══ REGION BARS ══ */
.reg-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .6rem; }
.reg-label { width: 100px; flex-shrink: 0; font-size: .78rem; color: var(--text2); }
.reg-track { flex: 1; height: 8px; background: var(--bg2); border-radius: 4px; overflow: hidden; }
.reg-fill  { height: 100%; border-radius: 4px; background: var(--red); }
.reg-count {
  width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
  background: var(--red-m); display: flex; align-items: center; justify-content: center;
  font-size: .75rem; font-weight: 700; color: var(--red);
}

/* ══ TABLE ══ */
.mc-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.mc-table th {
  text-align: left; padding: .6rem .85rem;
  color: var(--gray); font-weight: 600; font-size: .73rem;
  text-transform: uppercase; letter-spacing: .07em;
  background: var(--bg); border-bottom: 1.5px solid var(--border);
}
.mc-table td { padding: .65rem .85rem; border-bottom: 1px solid var(--border2); }
.mc-table tr:last-child td { border-bottom: none; }
.mc-table tr:hover td { background: #FAFAF8; }

/* ══ BADGE ══ */
.mc-badge-active   { background: var(--green-m); color: var(--green); padding: .2rem .65rem; border-radius: 99px; font-size: .75rem; font-weight: 600; }
.mc-badge-inactive { background: var(--bg2); color: var(--gray); padding: .2rem .65rem; border-radius: 99px; font-size: .75rem; font-weight: 600; }

/* ══ SECTION DIVIDER ══ */
.mc-divider { display: flex; align-items: center; gap: .75rem; margin: .5rem 0 1.25rem; }
.mc-divider span { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--gray); white-space: nowrap; }
.mc-divider::before, .mc-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* ══ RESPONSIVE ══ */
@media (max-width: 1024px) { .mc-stats { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 768px)  {
  .mc-sidebar { display: none; }
  .mc-section { grid-template-columns: 1fr; }
  .mc-stats   { grid-template-columns: repeat(2,1fr); }
}
</style>
</head>
<body>

<!-- ══ NAV ══ -->
<nav class="mc-nav">
  <div class="mc-nav-left">
    <div class="mc-nav-logo">
      <img src="mccain_logo.png" alt="McCain"
           onerror="this.style.display='none';document.getElementById('nl').style.display='block'">
      <div id="nl" class="logo-text" style="display:none">Mc<span>CAIN</span></div>
    </div>
    <div class="mc-nav-divider"></div>
    <span class="mc-nav-title">MAYA · Portal</span>
  </div>
  <div class="mc-nav-right">
    <span class="mc-nav-user">Hola, <strong><?= htmlspecialchars($user['nombre']) ?></strong></span>
    <a href="mccain_logout.php" class="mc-nav-logout">Salir</a>
  </div>
</nav>

<div class="mc-layout">

  <!-- ══ SIDEBAR ══ -->
  <aside class="mc-sidebar">
    <div class="mc-sidebar-section">
      <div class="mc-sidebar-label">Menú</div>
      <a href="mccain_dashboard.php" class="nav-link" id="lnk-dash">
        <span class="s-icon">📊</span> Dashboard
      </a>
      <a href="mccain_reportes.php" class="nav-link" id="lnk-rep">
        <span class="s-icon">📈</span> Reportes
      </a>
    </div>
    <div class="mc-sidebar-section">
      <div class="mc-sidebar-label">Info</div>
      <div style="padding:.5rem 1.1rem">
        <div style="font-size:.75rem;color:var(--text2);line-height:1.6">
          <strong style="color:var(--red)">MAYA</strong><br>
          Asistente de Vendedores<br>
          <span style="color:var(--gray)">Demo · <?= date('d/m/Y') ?></span>
        </div>
        <div style="margin-top:.75rem;padding:.6rem .85rem;background:var(--yellow-l);border:1px solid rgba(255,199,44,.4);border-radius:9px">
          <div style="font-size:.68rem;font-weight:700;color:var(--yellow-d);text-transform:uppercase;letter-spacing:.07em">API Key</div>
          <div style="font-size:.7rem;font-family:monospace;color:var(--text2);margin-top:.2rem;word-break:break-all">maya-demo-2026</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- ══ MAIN ══ -->
  <main class="mc-main">

    <div class="mc-page-header">
      <div>
        <h1>Dashboard <span class="h-badge">Demo</span></h1>
        <p class="mc-page-sub">Monitor de consultas de vendedores McCain · <?= date('d/m/Y H:i') ?></p>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="mc-stats">
      <div class="mc-stat" style="--stat-color:var(--red);--stat-bg:var(--red-l)">
        <div class="mc-stat-icon">💬</div>
        <div class="mc-stat-body">
          <div class="mc-stat-num"><?= $totalConsultas ?></div>
          <div class="mc-stat-label">Consultas totales</div>
        </div>
      </div>
      <div class="mc-stat" style="--stat-color:var(--green);--stat-bg:var(--green-l)">
        <div class="mc-stat-icon">👥</div>
        <div class="mc-stat-body">
          <div class="mc-stat-num"><?= $vendActivos ?></div>
          <div class="mc-stat-label">Vendedores activos</div>
        </div>
      </div>
      <div class="mc-stat" style="--stat-color:var(--yellow-d);--stat-bg:var(--yellow-l)">
        <div class="mc-stat-icon">📅</div>
        <div class="mc-stat-body">
          <div class="mc-stat-num"><?= $consultasHoy ?></div>
          <div class="mc-stat-label">Consultas hoy</div>
        </div>
      </div>
      <div class="mc-stat" style="--stat-color:var(--blue);--stat-bg:var(--blue-l)">
        <div class="mc-stat-icon">🆕</div>
        <div class="mc-stat-body">
          <div class="mc-stat-num"><?= $nuevosMes ?></div>
          <div class="mc-stat-label">Nuevos este mes</div>
        </div>
      </div>
    </div>

    <!-- Section: Temas + Interacciones -->
    <div class="mc-section">

      <!-- Card: Top temas + actividad -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Top temas consultados</h2>
          <span class="mc-badge-sm"><?= $totalLogs ?> total</span>
        </div>

        <!-- Activity mini chart -->
        <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--gray);margin-bottom:.4rem">
          Actividad — últimos 7 días
        </div>
        <div class="act-wrap">
          <div class="act-bars">
            <?php foreach ($porDia7 as $fecha => $cnt):
              $h = $maxDia7 > 0 ? max(4, round($cnt/$maxDia7*64)) : 4;
              $dm = date('d/m', strtotime($fecha));
            ?>
              <div class="act-bar" style="height:<?= $h ?>px">
                <div class="tip"><?= $dm ?>: <?= $cnt ?></div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="act-labels">
            <?php foreach ($porDia7 as $fecha => $cnt): ?>
              <span><?= date('d', strtotime($fecha)) ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="mc-divider" style="margin-top:1.2rem"><span>Por tipo</span></div>

        <?php foreach ($topTipos as $tipo => $cnt):
          $pct = $totalLogs > 0 ? round($cnt/$totalLogs*100) : 0;
          $col = mcTipoColor($tipo);
        ?>
        <div class="tipo-bar">
          <span class="tipo-label"><?= mcTipoLabel($tipo) ?></span>
          <div class="tipo-track"><div class="tipo-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
          <span class="tipo-val"><?= $cnt ?></span>
          <span class="tipo-pct"><?= $pct ?>%</span>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Card: Últimas interacciones -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Últimas interacciones</h2>
          <span class="mc-badge-sm">Tiempo real</span>
        </div>
        <?php foreach ($recientes as $log):
          $nombre = $log['vendedor_id'] ? mcVendedorNombre($log['vendedor_id']) : 'Sin registrar';
          $inicial = mb_strtoupper(mb_substr($nombre, 0, 1));
        ?>
        <div class="feed-item">
          <div class="feed-avatar"><?= $inicial ?></div>
          <div class="feed-body">
            <div class="feed-name"><?= htmlspecialchars($nombre) ?></div>
            <div class="feed-msg"><?= htmlspecialchars(mb_strimwidth($log['mensaje_usuario'], 0, 52, '…')) ?></div>
          </div>
          <div class="feed-meta">
            <div class="feed-time"><?= mcFormatDateTime($log['created_at']) ?></div>
            <div class="feed-tipo"><?= mcTipoLabel($log['tipo_consulta']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div><!-- /mc-section -->

    <!-- Section: Vendedores por región -->
    <div class="mc-card">
      <div class="mc-card-header">
        <h2>Distribución de vendedores por región</h2>
        <span class="mc-badge-sm"><?= $totalVendedores ?> registrados · <?= $vendActivos ?> activos</span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">
        <!-- Bars -->
        <div>
          <?php
          $rColors = ['#C8102E','#E6A800','#2563EB','#16A34A','#7C3AED','#0891B2'];
          $ri = 0;
          foreach ($porRegion as $reg => $cnt):
            $pct = round($cnt/$maxRegion*100);
            $col = $rColors[$ri++ % count($rColors)];
          ?>
          <div class="reg-row">
            <span class="reg-label"><?= htmlspecialchars($reg) ?></span>
            <div class="reg-track"><div class="reg-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
            <div class="reg-count" style="background:<?= $col ?>20;color:<?= $col ?>"><?= $cnt ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- Table -->
        <table class="mc-table" style="font-size:.8rem">
          <thead><tr><th>Vendedor</th><th>Región</th><th>Estado</th></tr></thead>
          <tbody>
          <?php foreach (array_slice($MCCAIN_VENDEDORES, 0, 7, true) as $v): ?>
            <tr>
              <td style="font-weight:600"><?= htmlspecialchars($v['nombre']) ?></td>
              <td><?= htmlspecialchars($v['region']) ?></td>
              <td>
                <?php if ($v['estado'] === 'activo'): ?>
                  <span class="mc-badge-active">● Activo</span>
                <?php else: ?>
                  <span class="mc-badge-inactive">● Inactivo</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script>
// Active sidebar link
document.querySelectorAll('.mc-sidebar a').forEach(a => {
  if (a.href.includes('dashboard')) a.classList.add('active');
});
</script>
</body>
</html>
