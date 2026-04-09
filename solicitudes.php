<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
$rol  = $user['rol'];

$sols = solicitudesFiltradas();

// Filtros
$filtroEstado = $_GET['estado'] ?? '';
$filtroTipo   = $_GET['tipo']   ?? '';

if ($filtroEstado) {
    $sols = array_filter($sols, fn($s) => $s['estado'] === $filtroEstado);
}
if ($filtroTipo) {
    $sols = array_filter($sols, fn($s) => $s['tipo'] === $filtroTipo);
}

usort($sols, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAX · Solicitudes</title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>
<div class="layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <h1>Solicitudes</h1>
        <p class="page-sub"><?= count($sols) ?> resultado<?= count($sols) != 1 ? 's' : '' ?> encontrados</p>
      </div>
    </div>

    <!-- Filtros -->
    <form method="GET" style="margin-bottom:1.5rem;">
      <div class="form-grid">
        <div class="form-group">
          <label>Estado</label>
          <select name="estado" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <?php foreach ($ESTADOS as $k => $e): ?>
              <option value="<?= $k ?>" <?= $filtroEstado === $k ? 'selected' : '' ?>>
                <?= htmlspecialchars($e['label']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tipo</label>
          <select name="tipo" onchange="this.form.submit()">
            <option value="">Todos los tipos</option>
            <?php foreach ($TIPOS_SOLICITUD as $k => $t): ?>
              <option value="<?= $k ?>" <?= $filtroTipo === $k ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['label']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php if ($filtroEstado || $filtroTipo): ?>
          <div class="form-group" style="justify-content:flex-end;padding-top:1.4rem;">
            <a href="solicitudes.php" class="btn-secondary">Limpiar filtros</a>
          </div>
        <?php endif; ?>
      </div>
    </form>

    <div class="panel">
      <?php if (empty($sols)): ?>
        <div class="empty-state">
          <div class="es-icon">📭</div>
          <p>No hay solicitudes que coincidan con los filtros aplicados.</p>
        </div>
      <?php else: ?>
      <table class="data-table">
        <thead>
          <tr>
            <th>Código</th>
            <?php if (in_array($rol, ['rrhh','lider','finanzas'])): ?><th>Empleado</th><?php endif; ?>
            <th>Tipo</th>
            <th>Desde</th>
            <th>Hasta</th>
            <th>Días</th>
            <th>Canal</th>
            <th>Estado</th>
            <th>Creada</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sols as $s): ?>
          <tr>
            <td><code><?= htmlspecialchars($s['codigo']) ?></code></td>
            <?php if (in_array($rol, ['rrhh','lider','finanzas'])): ?>
              <td><?= htmlspecialchars(empleadoNombre($s['empleado_id'])) ?></td>
            <?php endif; ?>
            <td><?= tipoLabel($s['tipo']) ?></td>
            <td><?= formatDate($s['fecha_inicio']) ?></td>
            <td><?= $s['fecha_fin'] !== $s['fecha_inicio'] ? formatDate($s['fecha_fin']) : '—' ?></td>
            <td><?= $s['dias'] ?: '—' ?></td>
            <td>
              <?php if ($s['canal'] === 'whatsapp'): ?>
                <span class="badge" style="background:rgba(34,197,94,.1);color:#4ade80">📱 WhatsApp</span>
              <?php else: ?>
                <span class="badge" style="background:rgba(59,130,246,.1);color:#60a5fa">🌐 Portal</span>
              <?php endif; ?>
            </td>
            <td><?= estadoBadge($s['estado']) ?></td>
            <td style="color:var(--gray)"><?= formatDate($s['created_at']) ?></td>
            <td><a href="detalle-solicitud.php?id=<?= $s['id'] ?>" class="tbl-link">Ver detalle →</a></td>
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
