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

// Avg diario (período completo ~35 días)
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
<title>MAYA · Reportes — McCain</title>
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
  --purple:    #7C3AED;
  --purple-l:  #F5F3FF;
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
.mc-sidebar a:hover  { color: var(--red); background: var(--red-l); }
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
.mc-stat-num   { font-size: 1.8rem; font-weight: 800; color: var(--text); line-height: 1; }
.mc-stat-label { font-size: .74rem; color: var(--text2); margin-top: .25rem; font-weight: 500; }

/* ══ REPORT GRID ══ */
.rep-grid   { display: grid; grid-template-columns: repeat(2,1fr); gap: 1.25rem; margin-bottom: 1.25rem; }
.rep-grid.triple { grid-template-columns: repeat(3,1fr); }

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

/* ══ BAR CHART (horizontal) ══ */
.bar-chart  { display: flex; flex-direction: column; gap: .6rem; }
.bar-item   { display: flex; align-items: center; gap: .75rem; }
.bar-label  { width: 130px; flex-shrink: 0; font-size: .78rem; color: var(--text2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bar-track  { flex: 1; height: 8px; background: var(--bg2); border-radius: 4px; overflow: hidden; }
.bar-fill   { height: 100%; border-radius: 4px; transition: width .5s; }
.bar-val    { width: 28px; text-align: right; font-size: .8rem; font-weight: 700; color: var(--text); }

/* ══ COLUMN CHART (vertical) ══ */
.col-chart  { display: flex; align-items: flex-end; gap: .28rem; height: 90px; }
.col-bar    {
  flex: 1; border-radius: 4px 4px 0 0; background: var(--red);
  opacity: .7; min-height: 3px; cursor: pointer; position: relative;
  transition: opacity .15s;
}
.col-bar:hover { opacity: 1; }
.col-bar .tip {
  display: none; position: absolute; bottom: calc(100% + 5px); left: 50%;
  transform: translateX(-50%); background: var(--text); color: #fff;
  font-size: .68rem; padding: .2rem .5rem; border-radius: 5px;
  white-space: nowrap; z-index: 10;
}
.col-bar:hover .tip { display: block; }
.col-labels { display: flex; gap: .28rem; margin-top: .3rem; }
.col-labels span { flex: 1; text-align: center; font-size: .58rem; color: var(--gray); }
.col-total  { text-align: center; font-size: .72rem; color: var(--text2); margin-top: .5rem; }

/* ══ DONUT ══ */
.donut-wrap { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; margin-top: .5rem; }
.legend     { display: flex; flex-direction: column; gap: .55rem; }
.legend-item { display: flex; align-items: center; gap: .5rem; font-size: .82rem; color: var(--text2); }
.legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-note { font-size: .75rem; color: var(--gray); margin-top: .4rem; line-height: 1.5; }

/* ══ SEMANA CHART ══ */
.semana-wrap { display: flex; align-items: flex-end; gap: .75rem; height: 90px; }
.sem-col     { flex: 1; display: flex; flex-direction: column; align-items: center; gap: .3rem; }
.sem-num     { font-size: .72rem; font-weight: 700; color: var(--text); }
.sem-bar     { width: 100%; border-radius: 4px 4px 0 0; background: var(--yellow); opacity: .85; }
.sem-label   { font-size: .68rem; color: var(--gray); }

/* ══ TABLE ══ */
.mc-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.mc-table th {
  text-align: left; padding: .6rem .85rem;
  color: var(--gray); font-weight: 600; font-size: .73rem;
  text-transform: uppercase; letter-spacing: .07em;
  background: var(--bg); border-bottom: 1.5px solid var(--border);
}
.mc-table td { padding: .65rem .85rem; border-bottom: 1px solid var(--border2); color: var(--text2); }
.mc-table tr:last-child td { border-bottom: none; }
.mc-table tr:hover td { background: #FAFAF8; }
.mc-table td strong { color: var(--text); }

/* ══ BADGE ══ */
.mc-badge-active   { background: var(--green-m); color: var(--green); padding: .2rem .65rem; border-radius: 99px; font-size: .75rem; font-weight: 600; }
.mc-badge-inactive { background: var(--bg2); color: var(--gray); padding: .2rem .65rem; border-radius: 99px; font-size: .75rem; font-weight: 600; }
.mc-badge-tipo     { background: var(--yellow-l); color: var(--yellow-d); border: 1px solid rgba(255,199,44,.4); padding: .15rem .55rem; border-radius: 99px; font-size: .72rem; font-weight: 600; }

/* ══ SECTION DIVIDER ══ */
.mc-divider { display: flex; align-items: center; gap: .75rem; margin: 1.25rem 0; }
.mc-divider span { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--gray); white-space: nowrap; }
.mc-divider::before, .mc-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* ══ HIGHLIGHT NUMBER ══ */
.hl-num { color: var(--red); font-weight: 700; }

/* ══ RESPONSIVE ══ */
@media (max-width: 1100px) {
  .mc-stats    { grid-template-columns: repeat(2,1fr); }
  .rep-grid.triple { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 768px) {
  .mc-sidebar  { display: none; }
  .rep-grid, .rep-grid.triple { grid-template-columns: 1fr; }
  .mc-stats    { grid-template-columns: repeat(2,1fr); }
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
      <a href="mccain_dashboard.php" id="lnk-dash">
        <span class="s-icon">📊</span> Dashboard
      </a>
      <a href="mccain_reportes.php" id="lnk-rep">
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
        <h1>Reportes <span class="h-badge">Demo</span></h1>
        <p class="mc-page-sub">Métricas de uso del chatbot MAYA · Datos ficticios · <?= date('d/m/Y') ?></p>
      </div>
    </div>

    <!-- ── KPIs ── -->
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
          <div class="mc-stat-num"><?= $numUnicos ?></div>
          <div class="mc-stat-label">Usuarios únicos</div>
        </div>
      </div>
      <div class="mc-stat" style="--stat-color:var(--yellow-d);--stat-bg:var(--yellow-l)">
        <div class="mc-stat-icon">📊</div>
        <div class="mc-stat-body">
          <div class="mc-stat-num"><?= $avgDiario ?></div>
          <div class="mc-stat-label">Promedio consultas/día</div>
        </div>
      </div>
      <div class="mc-stat" style="--stat-color:var(--blue);--stat-bg:var(--blue-l)">
        <div class="mc-stat-icon">👤</div>
        <div class="mc-stat-body">
          <div class="mc-stat-num"><?= $vendActivos ?><span style="font-size:1rem;color:var(--gray);font-weight:500"> / <?= $totalVendedores ?></span></div>
          <div class="mc-stat-label">Vendedores activos</div>
        </div>
      </div>
    </div>

    <!-- ── Fila 1: Tipos + Actividad 14 días ── -->
    <div class="rep-grid" style="margin-bottom:1.25rem">

      <!-- Consultas por tipo -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Consultas por tipo</h2>
          <span class="mc-badge-sm"><?= $totalConsultas ?> total</span>
        </div>
        <div class="bar-chart">
          <?php foreach ($porTipo as $tipo => $cnt):
            $pct = round($cnt / $maxTipo * 100);
            $col = mcTipoColor($tipo);
            $porcentaje = $totalConsultas > 0 ? round($cnt / $totalConsultas * 100) : 0;
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= mcTipoLabel($tipo) ?></span>
            <div class="bar-track">
              <div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div>
            </div>
            <span class="bar-val"><?= $cnt ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Actividad últimos 14 días -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Actividad — últimos 14 días</h2>
          <span class="mc-badge-sm"><?= array_sum($porDia14) ?> consultas</span>
        </div>
        <div class="col-chart">
          <?php foreach ($porDia14 as $fecha => $cnt):
            $h = $maxDia > 0 ? max(3, round($cnt / $maxDia * 90)) : 3;
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
        <div class="col-total">
          Pico máximo: <span class="hl-num"><?= $maxDia ?></span> consultas en un día
        </div>
      </div>

    </div>

    <!-- ── Fila 2: Canal + Región + Top vendedores ── -->
    <div class="rep-grid triple" style="margin-bottom:1.25rem">

      <!-- Canal de origen -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Canal de origen</h2>
        </div>
        <div class="donut-wrap">
          <?php $r = 50; $circ = round(2 * M_PI * $r, 1); ?>
          <svg width="120" height="120" viewBox="0 0 130 130" style="flex-shrink:0">
            <circle cx="65" cy="65" r="<?= $r ?>" fill="none" stroke="var(--bg2)" stroke-width="14"/>
            <circle cx="65" cy="65" r="<?= $r ?>" fill="none" stroke="var(--green)" stroke-width="14"
              stroke-dasharray="<?= $circ ?> 0"
              stroke-dashoffset="<?= round($circ/4, 1) ?>" stroke-linecap="round"/>
            <text x="65" y="61" text-anchor="middle" fill="var(--text)" font-size="17" font-weight="800" font-family="Inter,sans-serif">100%</text>
            <text x="65" y="76" text-anchor="middle" fill="var(--gray)" font-size="8.5" font-family="Inter,sans-serif">WhatsApp</text>
          </svg>
          <div class="legend">
            <div class="legend-item">
              <div class="legend-dot" style="background:var(--green)"></div>
              <span>📱 WhatsApp: <?= $totalConsultas ?></span>
            </div>
            <p class="legend-note">
              MAYA opera 100% vía<br>
              WhatsApp con Evolution API
            </p>
          </div>
        </div>
      </div>

      <!-- Vendedores por región -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Vendedores por región</h2>
          <span class="mc-badge-sm"><?= $totalVendedores ?> total</span>
        </div>
        <div class="bar-chart">
          <?php
          $rColors = ['#C8102E','#E6A800','#2563EB','#16A34A','#7C3AED','#0891B2','#EA580C'];
          $ri = 0;
          foreach ($porRegion as $region => $cnt):
            $pct = round($cnt / $maxRegion * 100);
            $col = $rColors[$ri++ % count($rColors)];
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= htmlspecialchars($region) ?></span>
            <div class="bar-track">
              <div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div>
            </div>
            <span class="bar-val"><?= $cnt ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Top vendedores por actividad -->
      <div class="mc-card">
        <div class="mc-card-header">
          <h2>Top vendedores</h2>
          <span class="mc-badge-sm">Por consultas</span>
        </div>
        <div class="bar-chart">
          <?php foreach ($topVendedores as $vid => $cnt):
            $pct = round($cnt / $maxVend * 100);
            $nombre = mcVendedorNombre($vid);
          ?>
          <div class="bar-item">
            <span class="bar-label"><?= htmlspecialchars($nombre) ?></span>
            <div class="bar-track">
              <div class="bar-fill" style="width:<?= $pct ?>%;background:var(--red)"></div>
            </div>
            <span class="bar-val"><?= $cnt ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- ── Crecimiento semanal de registros ── -->
    <div class="mc-card" style="margin-bottom:1.25rem">
      <div class="mc-card-header">
        <h2>Registros de vendedores por semana</h2>
        <span class="mc-badge-sm">Últimas 6 semanas</span>
      </div>
      <div class="semana-wrap">
        <?php foreach ($porSemana as $label => $cnt):
          $h = $maxSemana > 0 ? max(4, round($cnt / $maxSemana * 90)) : 4;
        ?>
        <div class="sem-col">
          <span class="sem-num"><?= $cnt ?></span>
          <div class="sem-bar" style="height:<?= $h ?>px"></div>
          <span class="sem-label"><?= $label ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ── Tabla detallada de vendedores ── -->
    <div class="mc-card">
      <div class="mc-card-header">
        <h2>Detalle de vendedores</h2>
        <span class="mc-badge-sm"><?= $totalVendedores ?> registrados</span>
      </div>
      <div style="overflow-x:auto">
        <table class="mc-table">
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
              <td><strong><?= htmlspecialchars($v['nombre']) ?></strong></td>
              <td style="font-family:monospace;font-size:.78rem"><?= htmlspecialchars($v['numero_whatsapp']) ?></td>
              <td><?= htmlspecialchars($v['region']) ?></td>
              <td>
                <?php if ($v['estado'] === 'activo'): ?>
                  <span class="mc-badge-active">● Activo</span>
                <?php else: ?>
                  <span class="mc-badge-inactive">● Inactivo</span>
                <?php endif; ?>
              </td>
              <td>
                <span style="font-weight:700;color:<?= $consultas > 3 ? 'var(--red)' : 'var(--text)' ?>">
                  <?= $consultas ?>
                </span>
              </td>
              <td style="font-size:.78rem"><?= mcFormatDate($v['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script>
// Link activo en sidebar
document.querySelectorAll('.mc-sidebar a').forEach(a => {
  if (a.href.includes('reportes')) a.classList.add('active');
});
</script>
</body>
</html>
