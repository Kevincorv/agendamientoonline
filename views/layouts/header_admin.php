<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$flash       = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$adminUser   = $_SESSION['admin_user'] ?? null;
$csrfToken   = generateCsrfToken();
$currentPath = str_replace(parse_url(APP_URL, PHP_URL_PATH), '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$pageMeta = [
  '/admin/dashboard'      => ['Dashboard', 'fa-tachometer-alt', 'Inicio'],
  '/admin/citas'          => ['Citas', 'fa-calendar-check', 'Gestión'],
  '/admin/medicos'        => ['Médicos', 'fa-user-md', 'Gestión'],
  '/admin/especialidades' => ['Especialidades', 'fa-stethoscope', 'Gestión'],
  '/admin/usuarios'       => ['Usuarios', 'fa-users-cog', 'Gestión'],
  '/admin/horarios'       => ['Horarios', 'fa-clock', 'Gestión'],
  '/admin/feriados'       => ['Feriados', 'fa-calendar-times', 'Gestión'],
  '/admin/auditoria'      => ['Auditoría', 'fa-history', 'Sistema'],
  '/admin/reportes'       => ['Reportes', 'fa-chart-bar', 'Sistema'],
  '/admin/backups'        => ['Backups', 'fa-database', 'Sistema'],
];
$currentPage = $pageMeta['/admin/dashboard'];
$currentBase = '/admin/dashboard';
foreach ($pageMeta as $path => $info) {
  if (str_starts_with($currentPath, $path)) { $currentPage = $info; $currentBase = $path; break; }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $currentPage[0] ?> — <?= APP_NAME ?? 'Clínica' ?></title>
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
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= APP_URL ?>/public/assets/app.js" defer></script>
<style>
  * { font-family: 'Plus Jakarta Sans', sans-serif; }
  .sidebar-transition { transition: margin-left .25s cubic-bezier(.4,0,.2,1); }
</style>
</head>
<body>

<div id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- ─── SIDEBAR ─── -->
<aside id="sidebar">
  <!-- Logo -->
  <div class="flex items-center gap-2.5 px-4 h-14 border-b border-slate-700/50 flex-shrink-0">
    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white flex-shrink-0"
         style="background:linear-gradient(135deg,#0284c7,#0e7490)">
      <i class="fas fa-hospital-alt text-xs"></i>
    </div>
    <div>
      <p class="text-white font-extrabold text-sm leading-tight"><?= APP_NAME ?? 'Clínica' ?></p>
      <p class="text-sky-400 text-[10px] font-semibold">Panel de Administración</p>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3">
    <p class="nav-group">Principal</p>
    <?php
    $navPrimary = [
      ['/admin/dashboard', 'fa-tachometer-alt', 'Dashboard'],
      ['/admin/citas',     'fa-calendar-check', 'Citas'],
    ];
    foreach ($navPrimary as [$url, $icon, $label]):
      $active = $currentBase === $url; ?>
    <a href="<?= APP_URL . $url ?>" class="nav-item <?= $active ? 'active' : '' ?>">
      <i class="fas <?= $icon ?>"></i>
      <span><?= $label ?></span>
    </a>
    <?php endforeach; ?>

    <p class="nav-group mt-3">Configuración</p>
    <?php
    $navConfig = [
      ['/admin/medicos',        'fa-user-md',     'Médicos'],
      ['/admin/especialidades', 'fa-stethoscope', 'Especialidades'],
      ['/admin/usuarios',       'fa-users-cog',   'Usuarios'],
      ['/admin/horarios',       'fa-clock',       'Horarios'],
    ];
    foreach ($navConfig as [$url, $icon, $label]):
      $active = str_starts_with($currentPath, $url); ?>
    <a href="<?= APP_URL . $url ?>" class="nav-item <?= $active ? 'active' : '' ?>">
      <i class="fas <?= $icon ?>"></i>
      <span><?= $label ?></span>
    </a>
    <?php endforeach; ?>

    <p class="nav-group mt-3">Sistema</p>
    <?php
    $navSystem = [
      ['/admin/feriados',    'fa-calendar-times', 'Feriados'],
      ['/admin/auditoria',   'fa-history',        'Auditoria'],
      ['/admin/reportes',    'fa-chart-bar',      'Reportes'],
      ['/admin/backups',     'fa-database',       'Backups'],
    ];
    foreach ($navSystem as [$url, $icon, $label]):
      $active = str_starts_with($currentPath, $url); ?>
    <a href="<?= APP_URL . $url ?>" class="nav-item <?= $active ? 'active' : '' ?>">
      <i class="fas <?= $icon ?>"></i>
      <span><?= $label ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- Bottom section -->
  <div class="px-3 py-3 border-t border-slate-700/50">
    <div class="flex items-center gap-3 px-3 py-2 mb-1">
      <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
           style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
        <?= strtoupper(substr($adminUser['nombre'] ?? 'A', 0, 1)) ?>
      </div>
      <div class="min-w-0 flex-1">
        <p class="text-white text-xs font-bold truncate"><?= htmlspecialchars($adminUser['nombre'] ?? 'Administrador') ?></p>
        <p class="text-slate-500 text-[10px]">Administrador</p>
      </div>
    </div>
    <a href="<?= APP_URL ?>/" target="_blank" class="nav-item mb-0.5">
      <i class="fas fa-external-link-alt"></i><span>Ver Sitio</span>
    </a>
    <a href="<?= APP_URL ?>/admin/logout" class="nav-item">
      <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
    </a>
    <p class="text-center text-[10px] text-slate-600 mt-2">v2.0.0</p>
  </div>
</aside>

<!-- ─── MAIN CONTENT ─── -->
<div class="main-content sidebar-transition min-h-screen flex flex-col" style="margin-left:250px">

  <!-- ─── TOPBAR ─── -->
  <header class="topbar gap-2">
    <button type="button" onclick="toggleSidebar()"
            class="md:hidden w-9 h-9 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 transition flex-shrink-0">
      <i class="fas fa-bars text-base"></i>
    </button>

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 min-w-0">
      <a href="<?= APP_URL ?>/admin/dashboard" class="text-slate-400 hover:text-sky-500 transition text-sm">
        <i class="fas fa-home"></i>
      </a>
      <span class="text-slate-300 text-xs">/</span>
      <span class="text-slate-700 font-bold text-sm truncate"><?= $currentPage[0] ?></span>
      <span class="text-slate-400 text-xs hidden sm:inline">· <?= $currentPage[2] ?></span>
    </div>

    <div class="flex-1"></div>

    <!-- Search (desktop) -->
    <div class="search-wrap hidden md:block">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Buscar..." id="globalSearch"
             onkeydown="if(event.key==='Enter'&&this.value.trim())performSearch(this.value.trim())">
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2">
      <!-- Notifications -->
      <div class="relative">
        <button type="button" class="btn btn-icon btn-ghost relative" onclick="toggleNotif()" id="notifBtn">
          <i class="far fa-bell"></i>
          <span id="notifBadge" class="notif-dot hidden">0</span>
        </button>
        <div id="notifDropdown" class="dropdown-menu" style="width:320px;right:0;max-height:400px;overflow-y:auto" onclick="event.stopPropagation()">
          <div class="px-3 py-2 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <p class="text-xs font-bold text-slate-700">Notificaciones</p>
            <button type="button" onclick="marcarLeidasNotif()" class="text-[10px] text-sky-600 hover:text-sky-800 font-semibold">
              <i class="fas fa-check-double"></i> Marcar leidas
            </button>
          </div>
          <div id="notifList" class="py-1"></div>
        </div>
      </div>

      <!-- User info -->
      <div class="flex items-center gap-2 px-2 py-1.5">
        <div class="avatar avatar-sm"
             style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
          <?= strtoupper(substr($adminUser['nombre'] ?? 'A', 0, 1)) ?>
        </div>
        <span class="text-sm font-semibold text-slate-700 hidden sm:block">
          <?= htmlspecialchars($adminUser['nombre'] ?? 'Admin') ?>
        </span>
      </div>
    </div>
  </header>

  <!-- ─── TOAST CONTAINER ─── -->
  <div id="toastContainer">  </div>

<script>
// ─── Notificaciones ───
function cargarNotificaciones() {
  fetch('<?= APP_URL ?>/admin/api/notificaciones')
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (!res.success) return;
      var badge = document.getElementById('notifBadge');
      var list = document.getElementById('notifList');
      if (badge) {
        badge.textContent = res.count;
        badge.classList.toggle('hidden', res.count === 0);
      }
      if (!list) return;
      if (!res.data || res.data.length === 0) {
        list.innerHTML = '<div class="py-6 text-center text-xs text-slate-400"><i class="far fa-bell-slash mr-1"></i> Sin notificaciones</div>';
        return;
      }
      var html = '';
      var tipoIcon = { info: 'fa-info-circle text-sky-500', warning: 'fa-exclamation-triangle text-amber-500', success: 'fa-check-circle text-emerald-500', danger: 'fa-times-circle text-red-500' };
      res.data.forEach(function(n) {
        var icon = tipoIcon[n.tipo] || 'fa-info-circle text-slate-400';
        html += '<div class="px-3 py-2.5 border-b border-slate-50 hover:bg-slate-50 transition cursor-pointer" onclick="marcarNotif(' + n.id + ', this)">' +
          '<div class="flex items-start gap-2.5">' +
            '<i class="fas ' + icon + ' mt-0.5"></i>' +
            '<div class="flex-1 min-w-0">' +
              '<p class="text-xs font-semibold text-slate-700">' + escHtml(n.titulo) + '</p>' +
              (n.mensaje ? '<p class="text-[11px] text-slate-400 truncate">' + escHtml(n.mensaje) + '</p>' : '') +
              '<p class="text-[10px] text-slate-300 mt-0.5">' + timeAgo(n.created_at) + '</p>' +
            '</div>' +
          '</div>' +
        '</div>';
      });
      list.innerHTML = html;
    })
    .catch(function() {});
}

function marcarNotif(id, el) {
  fetch('<?= APP_URL ?>/admin/api/notificaciones/marcar', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + id
  }).then(function() {
    el.style.opacity = '0.4';
    cargarNotificaciones();
  });
}

function marcarLeidasNotif() {
  fetch('<?= APP_URL ?>/admin/api/notificaciones/marcar-leidas', { method: 'POST' })
    .then(function() { cargarNotificaciones(); });
}

function timeAgo(dt) {
  if (!dt) return '';
  var d = new Date(dt.replace(' ', 'T') + 'Z');
  var now = new Date();
  var sec = Math.floor((now - d) / 1000);
  if (sec < 60) return 'ahora';
  var min = Math.floor(sec / 60);
  if (min < 60) return 'hace ' + min + 'm';
  var hr = Math.floor(min / 60);
  if (hr < 24) return 'hace ' + hr + 'h';
  var day = Math.floor(hr / 24);
  return 'hace ' + day + 'd';
}

// Cargar cada 30s
document.addEventListener('DOMContentLoaded', function() {
  cargarNotificaciones();
  setInterval(cargarNotificaciones, 30000);
});
</script>

  <!-- ─── MAIN ─── -->
  <main class="flex-1 p-5 lg:p-7" style="min-height:calc(100vh - 56px)">

  <?php if ($flash): ?>
  <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium mb-5 fade-up shadow-sm border
    <?= $flash['type'] === 'success'
        ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
        : 'bg-red-50 border-red-200 text-red-800' ?>">
    <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500' ?>"></i>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <!-- ─── DELETE MODAL ─── -->
  <div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)closeDeleteModal()">
    <div class="modal-box" style="max-width:400px">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-trash-alt text-red-500"></i>
        </div>
        <div class="flex-1">
          <h3 class="text-base font-extrabold text-slate-800 mb-1">Confirmar eliminación</h3>
          <p id="deleteModalText" class="text-sm text-slate-500">Esta acción no se puede deshacer.</p>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-6">
        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
        <form id="deleteModalForm" method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
          <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
        </form>
      </div>
    </div>
  </div>
