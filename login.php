<?php
session_start();
require_once __DIR__ . '/config.php';

if (isset($_SESSION['portal_user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if (isset($PORTAL_USERS[$usuario]) && password_verify($senha, $PORTAL_USERS[$usuario]['hash'])) {
        $u = $PORTAL_USERS[$usuario];
        $_SESSION['portal_user'] = array_merge($u, ['username' => $usuario]);
        unset($_SESSION['portal_user']['hash']);
        $_SESSION['portal_login_time'] = time();
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAX · Portal RRHH — Ingresar</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --navy:  #0a1628; --navy2: #0f2040;
    --blue:  #2563eb; --blue2: #3b82f6; --blue3: #60a5fa;
    --white: #f8fafc; --gray:  #94a3b8;
    --border: rgba(59,130,246,.2);
  }
  body {
    min-height: 100vh;
    background: var(--navy);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Segoe UI', system-ui, sans-serif;
    color: var(--white);
    padding: 1rem;
  }
  body::before {
    content: '';
    position: fixed; inset: 0;
    background: radial-gradient(ellipse 70% 50% at 50% 30%, rgba(37,99,235,.15) 0%, transparent 70%);
    pointer-events: none;
  }
  .card {
    background: rgba(15,32,64,.8);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    width: 100%; max-width: 400px;
    backdrop-filter: blur(10px);
    position: relative; z-index: 1;
  }
  .logo-wrap { text-align: center; margin-bottom: .75rem; }
  .logo-wrap img { height: 60px; object-fit: contain; filter: drop-shadow(0 0 16px rgba(37,99,235,.4)); }
  .company-tag {
    text-align: center;
    font-size: .7rem; font-weight: 700; letter-spacing: .12em;
    color: var(--blue2); text-transform: uppercase;
    margin-bottom: 1.5rem;
  }
  h1 { font-size: 1.25rem; font-weight: 700; text-align: center; margin-bottom: .35rem; }
  .subtitle { text-align: center; color: var(--gray); font-size: .875rem; margin-bottom: 2rem; }
  label {
    display: block; font-size: .82rem; font-weight: 600;
    color: var(--gray); margin-bottom: .35rem;
  }
  input[type=text], input[type=password] {
    width: 100%;
    background: rgba(255,255,255,.04);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    color: var(--white);
    padding: .7rem 1rem;
    font-size: .95rem;
    margin-bottom: 1.1rem;
    outline: none;
    transition: border-color .2s;
  }
  input:focus { border-color: var(--blue2); }
  input::placeholder { color: #475569; }
  .btn {
    width: 100%; padding: .8rem;
    background: var(--blue); color: #fff;
    border: none; border-radius: 10px;
    font-size: 1rem; font-weight: 700;
    cursor: pointer; transition: background .2s;
    margin-top: .25rem;
  }
  .btn:hover { background: #1d4ed8; }
  .error {
    background: rgba(239,68,68,.12);
    border: 1px solid rgba(239,68,68,.3);
    color: #fca5a5;
    border-radius: 8px; padding: .6rem .9rem;
    font-size: .85rem; margin-bottom: 1rem;
  }
  .hint {
    margin-top: 1.75rem;
    background: rgba(37,99,235,.08);
    border: 1px solid var(--border);
    border-radius: 10px; padding: 1rem;
  }
  .hint p { font-size: .78rem; color: var(--gray); margin-bottom: .5rem; font-weight: 600; }
  .hint table { width: 100%; border-collapse: collapse; }
  .hint td { font-size: .78rem; padding: .15rem .25rem; color: var(--gray); }
  .hint td:first-child { color: var(--blue3); font-weight: 600; width: 90px; }
  .back { display: block; text-align: center; margin-top: 1.25rem; font-size: .8rem; color: var(--gray); text-decoration: none; }
  .back:hover { color: var(--blue3); }
</style>
</head>
<body>
<div class="card">
  <div class="logo-wrap">
    <img src="../max_icon.png" alt="MAX">
  </div>
  <div class="company-tag">LL&amp;AA · Llerena &amp; Asociados</div>
  <h1>Portal RRHH</h1>
  <p class="subtitle">Ingresá con tu usuario y contraseña</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <label for="usuario">Usuario</label>
    <input type="text" id="usuario" name="usuario" required autofocus
           placeholder="ej: rrhh, lider1, empleado1"
           value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">

    <label for="senha">Contraseña</label>
    <input type="password" id="senha" name="senha" required placeholder="••••••••">

    <button type="submit" class="btn">Ingresar →</button>
  </form>

  <div class="hint">
    <p>Credenciales de demo (contraseña: demo2026)</p>
    <table>
      <tr><td>rrhh</td><td>Recursos Humanos — acceso completo</td></tr>
      <tr><td>lider1</td><td>Líder Derecho Laboral</td></tr>
      <tr><td>lider2</td><td>Líder Derecho Civil</td></tr>
      <tr><td>empleado1</td><td>Empleado — vista personal</td></tr>
      <tr><td>finanzas</td><td>Vista de finanzas y costos</td></tr>
    </table>
  </div>

  <a href="../max-rrhh.html" class="back">← Volver a la presentación</a>
</div>
</body>
</html>
