<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$flash = getFlash();
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= APP_NAME ?> — Login</title>
<meta name="theme-color" content="#0284c7">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?= APP_NAME ?? 'Clínica' ?>">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="manifest" href="<?= APP_URL ?>/public/manifest.json">
<link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/public/assets/icons/icon-192.svg">
<link rel="apple-touch-icon" href="<?= APP_URL ?>/public/assets/icons/icon-192.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  * { font-family: 'Plus Jakarta Sans', sans-serif; }
  .input-field {
    width:100%; border:2px solid #e2e8f0; border-radius:10px;
    padding:11px 14px; font-size:14px; transition:border-color .2s; outline:none;
  }
  .input-field:focus { border-color:#0284c7; }
  @keyframes fadeUp {
    from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)}
  }
  .fade-up { animation: fadeUp .4s ease both; }
</style>
</head>
<body style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0e7490 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:12px">

<div class="w-full max-w-md fade-up px-2 sm:px-0">

  <!-- Logo -->
  <div class="text-center mb-6 sm:mb-8">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl"
         style="background:linear-gradient(135deg,#0284c7,#0e7490)">
      <i class="fas fa-hospital-alt text-white text-2xl"></i>
    </div>
    <h1 class="text-white font-bold text-2xl"><?= APP_NAME ?></h1>
    <p class="text-sky-300 text-sm mt-1">Panel de Administración</p>
  </div>

  <!-- Card -->
  <div class="bg-white rounded-2xl shadow-2xl p-8">

    <?php if ($flash): ?>
    <div class="mb-5 flex items-center gap-3 p-3.5 rounded-xl text-sm font-medium
      <?= $flash['type'] === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-red-50 border border-red-200 text-red-800' ?>">
      <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500' ?>"></i>
      <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/admin/login" class="space-y-5" data-loading data-validate>
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Correo electrónico</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
            <i class="fas fa-envelope"></i>
          </span>
          <input type="email" name="email" required autofocus
                 class="input-field pl-10"
                 placeholder="correo@clinica.com">
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Contraseña</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
            <i class="fas fa-lock"></i>
          </span>
          <input type="password" name="password" id="pwd" required
                 class="input-field pl-10 pr-12"
                 placeholder="••••••••">
          <button type="button" onclick="var i=document.getElementById('pwd');i.type=i.type==='password'?'text':'password'"
                  class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
            <i class="fas fa-eye text-sm"></i>
          </button>
        </div>
      </div>

      <button type="submit"
              class="w-full py-3 rounded-xl font-bold text-white text-base transition-all shadow-lg hover:shadow-xl"
              style="background:linear-gradient(135deg,#0284c7,#0e7490)">
        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
      </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
      <a href="<?= APP_URL ?>/" class="text-slate-400 hover:text-sky-600 text-sm font-medium transition">
        <i class="fas fa-arrow-left mr-1"></i>Volver al sitio
      </a>
    </div>
  </div>
</div>

<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('<?= APP_URL ?>/public/sw.js');
}
</script>
</body>
</html>
