<?php
require_once __DIR__ . '/config.php';
requireRol(['rrhh','lider','finanzas']);
$user = currentUser();
$rol  = $user['rol'];

// Ver un empleado específico
$viewId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$viewEmp = $viewId ? ($EMPLEADOS[$viewId] ?? null) : null;

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
<title>MAX · Empleados</title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
<style>
.emp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 1.1rem; }
.emp-card {
  background: rgba(255,255,255,.03); border: 1px solid var(--border);
  border-radius: 14px; padding: 1.25rem; text-decoration: none; color: var(--white);
  transition: border-color .2s;
}
.emp-card:hover { border-color: rgba(59,130,246,.4); }
.emp-head { display: flex; align-items: center; gap: .9rem; margin-bottom: 1rem; }
.emp-avatar {
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--navy3); border: 2px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: 1.1rem; flex-shrink: 0;
}
.emp-info strong { display: block; font-size: .95rem; }
.emp-info span { font-size: .78rem; color: var(--gray); }
.emp-meta { display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; }
.emp-meta-item { font-size: .77rem; }
.emp-meta-item .em-label { color: var(--gray); }
.emp-meta-item .em-val { font-weight: 600; }
.dias-inline { display: flex; gap: .4rem; margin-top: .75rem; flex-wrap: wrap; }

/* Legajo detalle */
.legajo-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 1.25rem; }
.section-title { font-size: 1rem; font-weight: 700; margin-bottom: .9rem; }
@media (max-width: 768px) { .legajo-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>
<div class="layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <main class="main">

    <?php if ($viewEmp): ?>
    <!-- ── DETALLE EMPLEADO ── -->
    <div class="page-header">
      <div>
        <a href="empleados.php" class="btn-secondary" style="font-size:.8rem;padding:.35rem .85rem;margin-bottom:.6rem;display:inline-block">← Volver</a>
        <h1><?= htmlspecialchars($viewEmp['nombre']) ?></h1>
        <p class="page-sub"><?= htmlspecialchars($viewEmp['puesto']) ?> · <?= htmlspecialchars($viewEmp['area']) ?></p>
      </div>
    </div>

    <div class="legajo-grid">
      <div>
        <div class="panel" style="margin-bottom:1.25rem">
          <p class="section-title">Datos personales</p>
          <div class="detail-row"><span>Nombre completo</span><strong><?= htmlspecialchars($viewEmp['nombre']) ?></strong></div>
          <div class="detail-row"><span>DNI</span><strong><?= htmlspecialchars($viewEmp['dni']) ?></strong></div>
          <div class="detail-row"><span>Email</span><strong><?= htmlspecialchars($viewEmp['email']) ?></strong></div>
          <?php if ($rol === 'rrhh'): ?>
            <div class="detail-row"><span>WhatsApp</span><strong><?= htmlspecialchars($viewEmp['whatsapp']) ?></strong></div>
          <?php endif; ?>
          <div class="detail-row"><span>Fecha ingreso</span><strong><?= formatDate($viewEmp['fecha_ingreso']) ?></strong></div>
          <div class="detail-row"><span>Estado</span>
            <span class="badge badge-green"><?= ucfirst($viewEmp['estado']) ?></span>
          </div>
        </div>

        <div class="panel">
          <p class="section-title">Cargo y área</p>
          <div class="detail-row"><span>Puesto</span><strong><?= htmlspecialchars($viewEmp['puesto']) ?></strong></div>
          <div class="detail-row"><span>Área</span><strong><?= htmlspecialchars($viewEmp['area']) ?></strong></div>
          <?php if ($viewEmp['lider_id'] && isset($LIDERES[$viewEmp['lider_id']])): ?>
            <div class="detail-row"><span>Líder</span><strong><?= htmlspecialchars($LIDERES[$viewEmp['lider_id']]['nombre']) ?></strong></div>
          <?php endif; ?>
          <?php if (in_array($rol, ['rrhh','finanzas'])): ?>
            <div class="detail-row" style="border:none"><span>Sueldo mensual</span>
              <strong>$<?= number_format($viewEmp['sueldo'], 0, ',', '.') ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <div class="panel" style="margin-bottom:1.25rem">
          <p class="section-title">Control de días</p>
          <div class="dias-bars">
            <?php
            $vacLib = $viewEmp['dias_vacaciones_anuales'] - $viewEmp['dias_vacaciones_usados'];
            $vacPct = $viewEmp['dias_vacaciones_anuales'] > 0 ? round($vacLib/$viewEmp['dias_vacaciones_anuales']*100) : 0;
            $fraLib = $viewEmp['dias_franco_disponibles'] - $viewEmp['dias_franco_usados'];
            $fraPct = $viewEmp['dias_franco_disponibles'] > 0 ? round($fraLib/$viewEmp['dias_franco_disponibles']*100) : 0;
            ?>
            <div class="dias-bar-item">
              <div class="db-label">
                <span>Vacaciones disponibles</span>
                <strong><?= $vacLib ?> / <?= $viewEmp['dias_vacaciones_anuales'] ?> días</strong>
              </div>
              <div class="db-track"><div class="db-fill" style="width:<?= $vacPct ?>%;background:#3b82f6"></div></div>
            </div>
            <div class="dias-bar-item" style="margin-top:.6rem">
              <div class="db-label">
                <span>Francos disponibles</span>
                <strong><?= $fraLib ?> / <?= $viewEmp['dias_franco_disponibles'] ?> días</strong>
              </div>
              <div class="db-track"><div class="db-fill" style="width:<?= $fraPct ?>%;background:#06b6d4"></div></div>
            </div>
          </div>
          <div style="margin-top:1.1rem;display:grid;grid-template-columns:1fr 1fr;gap:.6rem;font-size:.82rem;">
            <div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:8px;padding:.6rem">
              <div style="color:var(--gray);margin-bottom:.2rem">Vac. usadas</div>
              <div style="font-weight:700"><?= $viewEmp['dias_vacaciones_usados'] ?> días</div>
            </div>
            <div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:8px;padding:.6rem">
              <div style="color:var(--gray);margin-bottom:.2rem">Francos usados</div>
              <div style="font-weight:700"><?= $viewEmp['dias_franco_usados'] ?> días</div>
            </div>
          </div>
        </div>

        <div class="panel">
          <p class="section-title">Solicitudes del empleado</p>
          <?php
          $empSols = array_filter($SOLICITUDES, fn($s) => $s['empleado_id'] === $viewEmp['id']);
          usort($empSols, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
          if (empty($empSols)):
          ?>
            <p style="font-size:.875rem;color:var(--gray)">Sin solicitudes registradas.</p>
          <?php else: ?>
          <table class="data-table">
            <thead><tr><th>Código</th><th>Tipo</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($empSols as $es): ?>
              <tr>
                <td><code><?= htmlspecialchars($es['codigo']) ?></code></td>
                <td><?= tipoLabel($es['tipo']) ?></td>
                <td><?= estadoBadge($es['estado']) ?></td>
                <td><a href="detalle-solicitud.php?id=<?= $es['id'] ?>" class="tbl-link">Ver →</a></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- ── LISTADO ── -->
    <div class="page-header">
      <div>
        <h1>Empleados</h1>
        <p class="page-sub"><?= count($lista) ?> empleado<?= count($lista) != 1 ? 's' : '' ?> encontrado<?= count($lista) != 1 ? 's' : '' ?></p>
      </div>
    </div>

    <div class="emp-grid">
      <?php foreach ($lista as $emp):
        $vacLib = $emp['dias_vacaciones_anuales'] - $emp['dias_vacaciones_usados'];
        $fraLib = $emp['dias_franco_disponibles'] - $emp['dias_franco_usados'];
      ?>
      <a href="empleados.php?id=<?= $emp['id'] ?>" class="emp-card">
        <div class="emp-head">
          <div class="emp-avatar"><?= strtoupper(substr($emp['nombre'],0,1)) ?></div>
          <div class="emp-info">
            <strong><?= htmlspecialchars($emp['nombre']) ?></strong>
            <span><?= htmlspecialchars($emp['puesto']) ?></span>
          </div>
        </div>
        <div class="emp-meta">
          <div class="emp-meta-item">
            <div class="em-label">Área</div>
            <div class="em-val"><?= htmlspecialchars($emp['area']) ?></div>
          </div>
          <div class="emp-meta-item">
            <div class="em-label">Ingreso</div>
            <div class="em-val"><?= formatDate($emp['fecha_ingreso']) ?></div>
          </div>
          <?php if (in_array($rol, ['rrhh','finanzas'])): ?>
          <div class="emp-meta-item">
            <div class="em-label">Sueldo</div>
            <div class="em-val">$<?= number_format($emp['sueldo'],0,',','.') ?></div>
          </div>
          <?php endif; ?>
        </div>
        <div class="dias-inline">
          <span class="badge" style="background:rgba(59,130,246,.12);color:#60a5fa">🏖️ <?= $vacLib ?>d vac</span>
          <span class="badge" style="background:rgba(6,182,212,.1);color:#67e8f9">📅 <?= $fraLib ?>d franco</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </main>
</div>
<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
</body>
</html>
