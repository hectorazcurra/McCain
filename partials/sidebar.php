<?php
$u   = currentUser();
$rol = $u['rol'] ?? '';
$cur = basename($_SERVER['PHP_SELF'], '.php');
function sideLink(string $page, string $icon, string $label, string $cur): void {
    $active = $cur === $page ? ' active' : '';
    echo '<a href="'.$page.'.php" class="'.$active.'"><span class="s-icon">'.$icon.'</span>'.$label.'</a>';
}
?>
<aside class="sidebar">
  <div class="sidebar-section">
    <div class="sidebar-label">Principal</div>
    <?php sideLink('dashboard', '🏠', 'Dashboard', $cur); ?>
    <?php sideLink('solicitudes', '📋', 'Solicitudes', $cur); ?>
  </div>

  <?php if (in_array($rol, ['rrhh','lider'])): ?>
  <div class="sidebar-section">
    <div class="sidebar-label">Gestión</div>
    <?php sideLink('empleados', '👥', 'Empleados', $cur); ?>
    <?php sideLink('historial', '📅', 'Control de días', $cur); ?>
    <?php if ($rol === 'rrhh'): ?>
      <?php sideLink('reportes', '📊', 'Reportes', $cur); ?>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($rol === 'finanzas'): ?>
  <div class="sidebar-section">
    <div class="sidebar-label">Finanzas</div>
    <?php sideLink('reportes', '📊', 'Reportes', $cur); ?>
    <?php sideLink('historial', '📅', 'Ausentismo', $cur); ?>
  </div>
  <?php endif; ?>

  <div class="sidebar-section">
    <div class="sidebar-label">Info</div>
    <a href="../max-rrhh.html" target="_blank">
      <span class="s-icon">🌐</span>Presentación
    </a>
  </div>
</aside>
