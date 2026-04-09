<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
$rol  = $user['rol'];

$id  = (int)($_GET['id'] ?? 0);
if (!isset($SOLICITUDES[$id])) {
    header('Location: solicitudes.php');
    exit;
}
$s   = $SOLICITUDES[$id];
$emp = $EMPLEADOS[$s['empleado_id']] ?? null;

// Acción de aprobar / rechazar (demo: solo muestra feedback visual)
$msg = '';
$msgType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'aprobar' && in_array($rol, ['rrhh','lider'])) {
        $msg = '✅ Solicitud aprobada (demo). En producción, esto actualiza la BD y notifica al empleado por WhatsApp.';
        $msgType = 'ok';
    } elseif ($accion === 'rechazar' && in_array($rol, ['rrhh','lider'])) {
        $motivo_rechazo = htmlspecialchars(trim($_POST['motivo_rechazo'] ?? 'Sin motivo indicado'));
        $msg = '❌ Solicitud rechazada (demo). Motivo: '.$motivo_rechazo;
        $msgType = 'err';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAX · <?= htmlspecialchars($s['codigo']) ?></title>
<?php include __DIR__ . '/partials/head-styles.php'; ?>
<style>
.detail-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem; }
.detail-row { display: flex; justify-content: space-between; padding: .55rem 0; border-bottom: 1px solid rgba(255,255,255,.04); font-size: .875rem; }
.detail-row span { color: var(--gray); }
.action-btns { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: 1rem; }
.reject-form { display: none; margin-top: 1rem; }
.reject-form textarea {
  width: 100%; background: rgba(255,255,255,.05); border: 1.5px solid var(--border);
  border-radius: 8px; color: var(--white); padding: .65rem .9rem; font-size: .875rem;
  resize: vertical; min-height: 70px; outline: none;
}
@media (max-width: 768px) { .detail-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>
<div class="layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <main class="main">

    <div class="page-header">
      <div>
        <a href="solicitudes.php" class="btn-secondary" style="font-size:.8rem;padding:.35rem .85rem;margin-bottom:.6rem;display:inline-block">← Volver</a>
        <h1><?= htmlspecialchars($s['codigo']) ?></h1>
        <p class="page-sub"><?= tipoLabel($s['tipo']) ?> · <?= estadoBadge($s['estado']) ?></p>
      </div>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
    <?php endif; ?>

    <div class="detail-grid">

      <!-- Datos de la solicitud -->
      <div>
        <div class="panel" style="margin-bottom:1.25rem">
          <div class="panel-header"><h2>Datos de la solicitud</h2></div>
          <div class="detail-row"><span>Código</span><strong><code><?= htmlspecialchars($s['codigo']) ?></code></strong></div>
          <div class="detail-row"><span>Tipo</span><strong><?= tipoLabel($s['tipo']) ?></strong></div>
          <div class="detail-row"><span>Estado</span><?= estadoBadge($s['estado']) ?></div>
          <div class="detail-row"><span>Canal</span>
            <?php if ($s['canal'] === 'whatsapp'): ?>
              <span class="badge" style="background:rgba(34,197,94,.1);color:#4ade80">📱 WhatsApp</span>
            <?php else: ?>
              <span class="badge" style="background:rgba(59,130,246,.1);color:#60a5fa">🌐 Portal</span>
            <?php endif; ?>
          </div>
          <div class="detail-row"><span>Fecha inicio</span><strong><?= formatDate($s['fecha_inicio']) ?></strong></div>
          <?php if ($s['fecha_fin'] !== $s['fecha_inicio']): ?>
            <div class="detail-row"><span>Fecha fin</span><strong><?= formatDate($s['fecha_fin']) ?></strong></div>
          <?php endif; ?>
          <?php if ($s['dias'] > 0): ?>
            <div class="detail-row"><span>Días</span><strong><?= $s['dias'] ?> día<?= $s['dias'] != 1 ? 's' : '' ?></strong></div>
          <?php endif; ?>
          <div class="detail-row"><span>Motivo</span><strong><?= htmlspecialchars($s['motivo']) ?></strong></div>
          <div class="detail-row"><span>Creada</span><strong><?= formatDateTime($s['created_at']) ?></strong></div>
          <div class="detail-row" style="border:none"><span>Última actualización</span><strong><?= formatDateTime($s['updated_at']) ?></strong></div>
        </div>

        <!-- Historial de auditoría -->
        <div class="panel">
          <div class="panel-header"><h2>Historial de acciones</h2></div>
          <div class="hist-timeline">
            <?php foreach ($s['historial'] as $h): ?>
            <div class="hist-item">
              <div class="hist-time"><?= formatDateTime($h['fecha']) ?></div>
              <div class="hist-body">
                <div class="hist-actor"><?= htmlspecialchars($h['actor']) ?></div>
                <div class="hist-action"><?= htmlspecialchars($h['accion']) ?></div>
                <div class="hist-badge"><?= estadoBadge($h['estado']) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Panel lateral -->
      <div>
        <!-- Empleado -->
        <?php if ($emp): ?>
        <div class="panel" style="margin-bottom:1.25rem">
          <div class="panel-header"><h2>Empleado</h2></div>
          <div class="detail-row"><span>Nombre</span><strong><?= htmlspecialchars($emp['nombre']) ?></strong></div>
          <div class="detail-row"><span>DNI</span><strong><?= htmlspecialchars($emp['dni']) ?></strong></div>
          <div class="detail-row"><span>Puesto</span><strong><?= htmlspecialchars($emp['puesto']) ?></strong></div>
          <div class="detail-row"><span>Área</span><strong><?= htmlspecialchars($emp['area']) ?></strong></div>
          <?php if (in_array($rol, ['rrhh'])): ?>
            <div class="detail-row"><span>WhatsApp</span><strong><?= htmlspecialchars($emp['whatsapp']) ?></strong></div>
          <?php endif; ?>
          <?php if ($s['tipo'] === 'vacaciones'): ?>
            <div class="detail-row" style="border:none">
              <span>Vacaciones disponibles</span>
              <strong><?= $emp['dias_vacaciones_anuales'] - $emp['dias_vacaciones_usados'] ?> / <?= $emp['dias_vacaciones_anuales'] ?> días</strong>
            </div>
          <?php elseif ($s['tipo'] === 'franco'): ?>
            <div class="detail-row" style="border:none">
              <span>Francos disponibles</span>
              <strong><?= $emp['dias_franco_disponibles'] - $emp['dias_franco_usados'] ?> días</strong>
            </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Acciones -->
        <?php if (in_array($rol, ['rrhh','lider']) && in_array($s['estado'], ['pendiente_lider','pendiente_rrhh'])): ?>
        <div class="panel">
          <div class="panel-header"><h2>Acciones disponibles</h2></div>
          <p style="font-size:.82rem;color:var(--gray);margin-bottom:.9rem">
            Esta solicitud está en estado <strong style="color:var(--white)"><?= $ESTADOS[$s['estado']]['label'] ?></strong> y requiere tu acción.
          </p>
          <form method="POST">
            <div class="action-btns">
              <button type="submit" name="accion" value="aprobar" class="btn-success">✅ Aprobar</button>
              <button type="button" class="btn-danger" onclick="document.getElementById('reject-form').style.display='block'">❌ Rechazar</button>
            </div>
            <div class="reject-form" id="reject-form">
              <textarea name="motivo_rechazo" placeholder="Motivo del rechazo (obligatorio)"></textarea>
              <button type="submit" name="accion" value="rechazar" class="btn-danger" style="margin-top:.5rem">Confirmar rechazo</button>
            </div>
          </form>
        </div>
        <?php elseif ($s['estado'] === 'aprobada'): ?>
        <div class="panel">
          <div class="panel-header"><h2>Estado final</h2></div>
          <p style="font-size:.875rem;color:#4ade80">✅ Esta solicitud fue aprobada y procesada correctamente.</p>
          <?php if ($s['tipo'] === 'certificado'): ?>
            <a href="#" class="btn-primary" style="margin-top:1rem;display:inline-block" onclick="alert('Demo: en producción descarga el PDF generado automáticamente.');return false">
              📄 Descargar certificado PDF
            </a>
          <?php endif; ?>
        </div>
        <?php elseif ($s['estado'] === 'rechazada'): ?>
        <div class="panel">
          <div class="panel-header"><h2>Estado final</h2></div>
          <p style="font-size:.875rem;color:#f87171">❌ Esta solicitud fue rechazada.</p>
        </div>
        <?php endif; ?>

        <!-- Acceso legajo -->
        <?php if (in_array($rol, ['rrhh']) && $emp): ?>
        <div style="margin-top:1rem">
          <a href="empleados.php?id=<?= $emp['id'] ?>" class="btn-secondary" style="display:block;text-align:center">
            👤 Ver legajo completo
          </a>
        </div>
        <?php endif; ?>
      </div>

    </div><!-- /detail-grid -->

  </main>
</div>
<?php include __DIR__ . '/partials/foot-scripts.php'; ?>
</body>
</html>
