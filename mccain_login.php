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
<title>MAYA · Portal McCain — Ingresar</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --red:     #C8102E;
  --red-d:   #A00020;
  --red-l:   #F5E8EB;
  --yellow:  #FFC72C;
  --yellow-d:#E6A800;
  --white:   #FFFFFF;
  --bg:      #F7F4F0;
  --text:    #1A1A1A;
  --text2:   #5C5C5C;
  --border:  #E0D8D0;
  --shadow:  0 4px 24px rgba(0,0,0,.10);
  --radius:  16px;
}

html, body {
  height: 100%;
  font-family: 'Inter', system-ui, sans-serif;
  background: var(--bg);
  color: var(--text);
}

/* ── Layout ── */
.login-page {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 480px;
}

/* ── Left hero panel ── */
.login-hero {
  background: var(--red);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 2.5rem;
  position: relative;
  overflow: hidden;
}
.login-hero::before {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(circle at 20% 80%, rgba(255,199,44,.18) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(0,0,0,.15) 0%, transparent 50%);
}
.hero-content { position: relative; z-index: 1; text-align: center; }
.hero-logo { margin-bottom: 2rem; }
.hero-logo img {
  height: 70px;
  object-fit: contain;
  filter: brightness(0) invert(1);
}
.hero-logo .logo-fallback {
  font-size: 3rem; font-weight: 900; letter-spacing: -.04em; color: #fff;
}
.hero-logo .logo-fallback span { color: var(--yellow); }

.hero-title {
  font-size: 1.7rem; font-weight: 800; color: #fff;
  line-height: 1.2; margin-bottom: .75rem;
}
.hero-sub {
  font-size: 1rem; color: rgba(255,255,255,.75);
  max-width: 320px; margin: 0 auto 2rem;
  line-height: 1.6;
}
.hero-badge {
  display: inline-flex; align-items: center; gap: .5rem;
  background: var(--yellow); color: #1a1a1a;
  font-size: .82rem; font-weight: 700;
  padding: .45rem 1rem; border-radius: 999px;
  letter-spacing: .03em;
}

.hero-dots {
  position: absolute; bottom: 2rem; left: 0; right: 0;
  display: flex; justify-content: center; gap: .5rem;
}
.hero-dots span {
  width: 8px; height: 8px; border-radius: 50%;
  background: rgba(255,255,255,.3);
}
.hero-dots span.active { background: var(--yellow); }

/* ── Right form panel ── */
.login-form-panel {
  background: var(--white);
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem 3.5rem;
  box-shadow: -4px 0 24px rgba(0,0,0,.06);
}

.form-header { margin-bottom: 2.25rem; }
.form-header .welcome {
  font-size: .8rem; font-weight: 600; color: var(--red);
  text-transform: uppercase; letter-spacing: .1em; margin-bottom: .5rem;
}
.form-header h1 {
  font-size: 1.75rem; font-weight: 800; color: var(--text);
  line-height: 1.15;
}
.form-header p {
  font-size: .875rem; color: var(--text2); margin-top: .4rem;
}

.form-group { margin-bottom: 1.25rem; }
.form-group label {
  display: block; font-size: .8rem; font-weight: 600;
  color: var(--text2); margin-bottom: .4rem;
  text-transform: uppercase; letter-spacing: .06em;
}
.form-group input {
  width: 100%; padding: .75rem 1rem;
  background: var(--bg); border: 1.5px solid var(--border);
  border-radius: 10px; color: var(--text);
  font-size: .95rem; font-family: inherit; outline: none;
  transition: border-color .2s, box-shadow .2s;
}
.form-group input:focus {
  border-color: var(--red);
  box-shadow: 0 0 0 3px rgba(200,16,46,.08);
  background: #fff;
}

.btn-login {
  width: 100%; padding: .85rem;
  background: var(--red); color: #fff;
  font-family: inherit; font-size: 1rem; font-weight: 700;
  border: none; border-radius: 10px; cursor: pointer;
  margin-top: .5rem;
  transition: background .2s, transform .1s, box-shadow .2s;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-login:hover  { background: var(--red-d); box-shadow: 0 4px 16px rgba(200,16,46,.35); }
.btn-login:active { transform: scale(.98); }

.error-box {
  display: flex; align-items: center; gap: .6rem;
  background: var(--red-l); border: 1.5px solid rgba(200,16,46,.25);
  border-radius: 10px; padding: .75rem 1rem;
  font-size: .875rem; color: var(--red);
  margin-bottom: 1.25rem; font-weight: 500;
}

.demo-hint {
  margin-top: 2rem; padding: 1.1rem 1.25rem;
  background: #FFFBF0; border: 1.5px solid rgba(255,199,44,.5);
  border-radius: 12px;
}
.demo-hint .dh-title {
  font-size: .72rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: .1em; color: #8B6C00; margin-bottom: .65rem;
  display: flex; align-items: center; gap: .4rem;
}
.demo-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: .2rem 0; font-size: .85rem; color: var(--text2);
}
.demo-row .val {
  font-family: 'Courier New', monospace; font-size: .82rem;
  background: rgba(255,199,44,.2); color: #6B4F00;
  padding: .15rem .6rem; border-radius: 5px; font-weight: 700;
}

.form-footer {
  margin-top: 2rem; text-align: center;
  font-size: .78rem; color: var(--text2);
}

/* ── Responsive ── */
@media (max-width: 860px) {
  .login-page { grid-template-columns: 1fr; }
  .login-hero  { display: none; }
  .login-form-panel { padding: 2rem 1.5rem; }
}
</style>
</head>
<body>
<div class="login-page">

  <!-- Left hero -->
  <div class="login-hero">
    <div class="hero-content">
      <div class="hero-logo">
        <img src="mccain_logo.png" alt="McCain"
             onerror="this.style.display='none';document.getElementById('hfl').style.display='block'">
        <div id="hfl" class="logo-fallback" style="display:none">Mc<span>CAIN</span></div>
      </div>
      <h2 class="hero-title">MAYA<br>Asistente de Vendedores</h2>
      <p class="hero-sub">Portal de monitoreo de consultas y métricas de uso del chatbot WhatsApp.</p>
      <div class="hero-badge">📊 Demo · Datos ficticios</div>
    </div>
    <div class="hero-dots">
      <span class="active"></span><span></span><span></span>
    </div>
  </div>

  <!-- Right form -->
  <div class="login-form-panel">
    <div class="form-header">
      <div class="welcome">Portal de administración</div>
      <h1>Iniciar sesión</h1>
      <p>Ingresá tus credenciales para acceder al dashboard.</p>
    </div>

    <?php if ($error): ?>
    <div class="error-box">
      <span>⚠️</span>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="mccain"
               value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
               autocomplete="username" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••"
               autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn-login">
        Ingresar al portal <span>→</span>
      </button>
    </form>

    <div class="demo-hint">
      <div class="dh-title">🔑 Acceso demo</div>
      <div class="demo-row"><span>Usuario</span><span class="val">mccain</span></div>
      <div class="demo-row"><span>Contraseña</span><span class="val">demo2026</span></div>
    </div>

    <div class="form-footer">
      McCain Foods · MAYA Vendedores Demo &nbsp;·&nbsp; <?= date('Y') ?>
    </div>
  </div>

</div>
</body>
</html>
