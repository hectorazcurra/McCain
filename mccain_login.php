<?php
// ══════════════════════════════════════════════════
//  MAYA · Login — Portal de Monitoreo McCain
// ══════════════════════════════════════════════════

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/mccain_config.php';

if (isset($_SESSION['mccain_user'])) {
    header('Location: mccain_dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if (isset($MCCAIN_ADMIN[$user]) && password_verify($pass, $MCCAIN_ADMIN[$user]['hash'])) {
        $_SESSION['mccain_user'] = [
            'usuario' => $user,
            'nombre'  => $MCCAIN_ADMIN[$user]['nombre'],
            'rol'     => $MCCAIN_ADMIN[$user]['rol'],
        ];
        header('Location: mccain_dashboard.php');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAYA · Portal McCain</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --red:    #C8102E;
  --red2:   #a50d26;
  --red3:   #e8405a;
  --navy:   #1a0005;
  --navy2:  #2d000a;
  --navy3:  #400010;
  --border: rgba(200,16,46,.22);
  --white:  #f8f4f4;
  --gray:   #9a8a8d;
  --text:   #e8d8da;
}
html, body {
  height: 100%;
  background: radial-gradient(ellipse at 60% 30%, #3a0010 0%, #1a0005 55%, #0d0002 100%);
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  color: var(--text);
}
.login-wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}
.login-card {
  width: 100%;
  max-width: 400px;
  background: rgba(200,16,46,.06);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 2.25rem 2.25rem 2rem;
  backdrop-filter: blur(8px);
  box-shadow: 0 8px 40px rgba(0,0,0,.45), 0 0 0 1px rgba(200,16,46,.08);
}
.login-logo {
  text-align: center;
  margin-bottom: 1.5rem;
}
.login-logo .brand {
  font-size: 2rem;
  font-weight: 900;
  letter-spacing: -.03em;
  color: #fff;
}
.login-logo .brand span { color: var(--red3); }
.login-logo .tagline {
  font-size: .78rem;
  color: var(--gray);
  margin-top: .25rem;
  letter-spacing: .08em;
  text-transform: uppercase;
}
.login-logo .subtitle {
  font-size: .7rem;
  color: rgba(200,16,46,.7);
  margin-top: .1rem;
  letter-spacing: .05em;
}
h2 {
  font-size: 1.05rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  text-align: center;
  color: var(--white);
}
.form-group { margin-bottom: 1rem; }
label {
  display: block;
  font-size: .78rem;
  font-weight: 600;
  color: var(--gray);
  margin-bottom: .4rem;
  text-transform: uppercase;
  letter-spacing: .07em;
}
input[type="text"], input[type="password"] {
  width: 100%;
  padding: .65rem .9rem;
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border);
  border-radius: 9px;
  color: var(--white);
  font-size: .9rem;
  outline: none;
  transition: border-color .2s;
}
input:focus { border-color: var(--red3); background: rgba(200,16,46,.05); }
button[type="submit"] {
  width: 100%;
  padding: .75rem;
  background: var(--red);
  color: #fff;
  font-size: .95rem;
  font-weight: 700;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  margin-top: .5rem;
  transition: background .2s, transform .1s;
  letter-spacing: .03em;
}
button:hover  { background: var(--red2); }
button:active { transform: scale(.98); }
.error-box {
  background: rgba(200,16,46,.12);
  border: 1px solid rgba(200,16,46,.35);
  border-radius: 8px;
  padding: .65rem .9rem;
  font-size: .83rem;
  color: #f87171;
  margin-bottom: 1rem;
  text-align: center;
}
.demo-hint {
  margin-top: 1.5rem;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 10px;
  padding: .9rem 1rem;
}
.demo-hint p {
  font-size: .72rem;
  color: var(--gray);
  text-transform: uppercase;
  letter-spacing: .08em;
  margin-bottom: .6rem;
  font-weight: 600;
}
.demo-row {
  display: flex;
  justify-content: space-between;
  font-size: .8rem;
  color: var(--text);
  padding: .25rem 0;
}
.demo-row span:last-child {
  font-family: monospace;
  color: var(--red3);
  background: rgba(200,16,46,.08);
  padding: .1rem .5rem;
  border-radius: 5px;
}
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <div class="brand">Mc<span>CAIN</span></div>
      <div class="tagline">MAYA · Asistente de Vendedores</div>
      <div class="subtitle">Portal de Monitoreo · Demo</div>
    </div>

    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
      <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" autocomplete="username"
               value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit">Ingresar →</button>
    </form>

    <div class="demo-hint">
      <p>Acceso demo</p>
      <div class="demo-row"><span>Usuario</span><span>mccain</span></div>
      <div class="demo-row"><span>Contraseña</span><span>demo2026</span></div>
    </div>
  </div>
</div>
</body>
</html>
