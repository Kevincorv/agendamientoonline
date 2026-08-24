<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= APP_NAME ?? 'Clínica' ?> - Sistema de Citas</title>
<meta name="theme-color" content="#0284c7">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?= APP_NAME ?? 'Clínica' ?>">
<link rel="manifest" href="<?= APP_URL ?>/public/manifest.json">
<link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/public/assets/icons/icon-192.svg">
<link rel="apple-touch-icon" href="<?= APP_URL ?>/public/assets/icons/icon-192.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/app.css">
<script src="<?= APP_URL ?>/public/assets/app.js" defer></script>
<style>
  * { font-family: 'Plus Jakarta Sans', sans-serif; }
  body { background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
</style>
</head>
<body>

<!-- Mobile Drawer -->
<div id="drawerOverlay" class="drawer-overlay" onclick="closeDrawer()"></div>
<div id="mobileDrawer" class="mobile-drawer">
  <div class="flex items-center justify-between px-5 py-5 border-b border-slate-100">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white"
           style="background:linear-gradient(135deg,#0284c7,#0e7490)">
        <i class="fas fa-heartbeat text-xs"></i>
      </div>
      <span class="font-extrabold text-slate-800 text-sm"><?= APP_NAME ?? 'Clínica' ?></span>
    </div>
    <button onclick="closeDrawer()" class="text-slate-400 hover:text-slate-600 text-lg">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <div class="flex-1 px-3 py-4">
    <a href="<?= APP_URL ?>/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 font-semibold hover:bg-sky-50 transition">
      <i class="fas fa-home text-sky-500 w-5 text-center"></i> Inicio
    </a>
    <a href="<?= APP_URL ?>/agendar" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 font-semibold hover:bg-sky-50 transition">
      <i class="fas fa-calendar-plus text-sky-500 w-5 text-center"></i> Agendar Cita
    </a>
    <hr class="my-3 border-slate-100">
    <a href="<?= APP_URL ?>/paciente/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-medium hover:bg-slate-50 transition">
      <i class="fas fa-user-injured w-5 text-center"></i> Mi Cuenta
    </a>
    <a href="<?= APP_URL ?>/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 font-medium hover:bg-slate-50 transition">
      <i class="fas fa-lock w-5 text-center"></i> Acceso Personal
    </a>
  </div>
</div>

<!-- Navbar -->
<nav id="mainNav" class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">
      <button onclick="openDrawer()" class="md:hidden w-9 h-9 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 transition">
        <i class="fas fa-bars text-base"></i>
      </button>

      <a href="<?= APP_URL ?>/" class="flex items-center gap-2.5 group">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white group-hover:shadow-lg transition-all"
             style="background:linear-gradient(135deg,#0284c7,#0e7490)">
          <i class="fas fa-heartbeat text-sm"></i>
        </div>
        <span class="font-extrabold text-slate-800 text-lg hidden sm:inline"><?= APP_NAME ?? 'Clínica' ?></span>
      </a>

      <div class="hidden md:flex items-center gap-1">
        <a href="<?= APP_URL ?>/" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Inicio</a>
        <a href="<?= APP_URL ?>/#especialidades" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Especialidades</a>
        <a href="<?= APP_URL ?>/#medicos" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Médicos</a>
        <a href="<?= APP_URL ?>/agendar" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">Agendar</a>
        <?php if (isset($_SESSION['paciente_id'])): ?>
        <a href="<?= APP_URL ?>/paciente/dashboard" class="px-4 py-2 rounded-xl text-sm font-semibold text-sky-600 hover:bg-sky-50 transition">Mi Cuenta</a>
        <?php endif; ?>
      </div>

      <div class="flex items-center gap-1.5 sm:gap-3">
        <a href="<?= APP_URL ?>/agendar"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold text-white shadow-md hover:shadow-lg transition-all"
           style="background:linear-gradient(135deg,#0284c7,#0e7490)">
          <i class="fas fa-calendar-plus"></i>
          <span class="hidden sm:inline">Agendar Cita</span>
        </a>
        <a href="<?= APP_URL ?>/paciente/login"
           class="text-slate-500 hover:text-sky-600 text-sm font-semibold transition flex items-center gap-1 px-2">
          <i class="fas fa-user-injured text-xs"></i>
          <span class="hidden sm:inline">Paciente</span>
        </a>
        <a href="<?= APP_URL ?>/admin"
           class="text-slate-400 hover:text-slate-700 text-sm font-medium transition flex items-center gap-1 px-2">
          <i class="fas fa-lock text-xs"></i>
          <span class="hidden sm:inline">Personal</span>
        </a>
      </div>
    </div>
  </div>
</nav>

<div id="toastContainer" class="toast-container"></div>
<?php if ($flash): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  showToast('<?= htmlspecialchars($flash['message'], ENT_QUOTES) ?>', '<?= $flash['type'] === 'success' ? 'success' : 'error' ?>');
});
</script>
<?php endif; ?>

<main class="flex-1">
