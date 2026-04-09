<?php $u = currentUser(); ?>
<nav class="topnav">
  <div class="topnav-brand">
    <img src="../max_icon.png" alt="MAX">
    <div>
      <div class="brand-name">MAX · Portal RRHH</div>
      <div class="brand-sub">LL&amp;AA Llerena &amp; Asociados</div>
    </div>
  </div>
  <div class="topnav-right">
    <span class="topnav-user">
      <strong><?= htmlspecialchars($u['nombre'] ?? '') ?></strong> · <?= rolLabel($u['rol'] ?? '') ?>
    </span>
    <a href="logout.php" class="topnav-logout">Salir</a>
  </div>
</nav>
