<?php
require_once __DIR__ . '/../../layouts/header_public.php';
$nombre  = $_SESSION['paciente_nombre'] ?? 'Paciente';
$initial = strtoupper(substr($nombre, 0, 1));
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 fade-up">
  <!-- Top bar: search + notifications + quick actions -->
  <div class="flex items-center gap-3 mb-6">
    <div class="relative flex-1">
      <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
      <input type="text" id="searchCitas" placeholder="Buscar por médico, especialidad, fecha..."
             class="w-full h-11 pl-10 pr-4 rounded-2xl border border-slate-200 bg-white/80 backdrop-blur-sm text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition">
      <button type="button" id="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition hidden">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Notifications -->
    <div class="relative" id="notifContainer">
      <button id="notifBtn" class="relative w-11 h-11 rounded-2xl flex items-center justify-center border border-slate-200 bg-white/80 backdrop-blur-sm text-slate-500 hover:bg-sky-50 hover:text-sky-600 hover:border-sky-200 transition">
        <i class="fas fa-bell text-lg"></i>
        <span id="notifBadge" class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center shadow-lg hidden">0</span>
      </button>
      <div id="notifDropdown" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden hidden z-50">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-bold text-slate-700">Notificaciones</h3>
          <button id="marcarLeidas" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition">Marcar todas leídas</button>
        </div>
        <div id="notifList" class="max-h-72 overflow-y-auto divide-y divide-slate-50">
          <div class="px-4 py-8 text-center text-slate-400 text-sm">Sin notificaciones</div>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <a href="<?= APP_URL ?>/agendar" class="h-11 px-5 rounded-2xl flex items-center gap-2 text-sm font-bold text-white shadow-md hover:shadow-lg transition-all"
       style="background:linear-gradient(135deg,#0284c7,#0e7490)">
      <i class="fas fa-plus"></i>
      <span class="hidden sm:inline">Nueva Cita</span>
    </a>
    <div class="relative group">
      <button class="w-11 h-11 rounded-2xl flex items-center justify-center border border-slate-200 bg-white/80 backdrop-blur-sm text-slate-500 hover:bg-slate-50 transition">
        <i class="fas fa-ellipsis-v"></i>
      </button>
      <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden hidden group-focus-within:block hover:block z-50"
           onclick="this.classList.remove('hidden')" onmouseleave="this.classList.add('hidden')">
        <a href="<?= APP_URL ?>/paciente/historial" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-600 hover:bg-sky-50 hover:text-sky-700 transition">
          <i class="fas fa-history w-4 text-center text-slate-400"></i> Historial
        </a>
        <a href="<?= APP_URL ?>/paciente/perfil" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-600 hover:bg-sky-50 hover:text-sky-700 transition">
          <i class="fas fa-user-cog w-4 text-center text-slate-400"></i> Mi Perfil
        </a>
        <hr class="border-slate-100">
        <a href="<?= APP_URL ?>/paciente/logout" class="flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition">
          <i class="fas fa-sign-out-alt w-4 text-center"></i> Cerrar sesión
        </a>
      </div>
    </div>
  </div>

  <!-- Welcome banner -->
  <div class="rounded-2xl p-5 sm:p-6 mb-6 text-white shadow-lg"
       style="background:linear-gradient(135deg,#0284c7,#0e7490)">
    <div class="flex items-center gap-4">
      <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl font-bold bg-white/20 backdrop-blur-sm flex-shrink-0 shadow-inner">
        <?= $initial ?>
      </div>
      <div>
        <h1 class="text-lg sm:text-xl font-extrabold">Bienvenido, <?= htmlspecialchars($nombre) ?></h1>
        <p class="text-sm text-white/70">Gestioná tus citas médicas de forma rápida y sencilla</p>
      </div>
    </div>
  </div>

  <!-- Próximas citas -->
  <div class="card mb-6">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-sm font-bold text-slate-700">
        <i class="fas fa-calendar-day text-sky-500 mr-2"></i>Próximas Citas
      </h2>
      <?php if (!empty($proximas)): ?>
      <a href="<?= APP_URL ?>/paciente/historial" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition whitespace-nowrap">
        Ver historial <i class="fas fa-arrow-right ml-1"></i>
      </a>
      <?php endif; ?>
    </div>

    <?php if (empty($proximas)): ?>
      <div class="text-center py-12 text-slate-400">
        <i class="far fa-calendar-check text-4xl mb-3 block mx-auto"></i>
        <p class="text-sm font-medium">No tenés citas próximas</p>
        <a href="<?= APP_URL ?>/agendar" class="btn btn-primary btn-sm mt-4 inline-flex">
          <i class="fas fa-plus"></i> Agendar Cita
        </a>
      </div>
    <?php else: ?>
      <div class="divide-y divide-slate-100" id="listaCitas">
        <?php foreach ($proximas as $c):
          $searchText = strtolower($c['especialidad'] . ' ' . $c['medico_nombre'] . ' ' . $c['medico_apellido'] . ' ' . $c['fecha'] . ' ' . $c['estado']);
        ?>
        <div class="cita-item px-5 py-4 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center gap-3"
             data-search="<?= htmlspecialchars($searchText, ENT_QUOTES) ?>">
          <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="text-center flex-shrink-0 w-14 py-1 rounded-lg bg-sky-50">
              <p class="text-lg font-extrabold text-sky-600 leading-tight"><?= date('d', strtotime($c['fecha'])) ?></p>
              <p class="text-[10px] text-sky-500 font-bold uppercase"><?= ucfirst(date('M', strtotime($c['fecha']))) ?></p>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-bold text-slate-800 truncate cita-especialidad"><?= htmlspecialchars($c['especialidad']) ?></p>
              <p class="text-xs text-slate-500 mt-0.5">
                <i class="far fa-clock mr-1"></i><?= substr($c['hora'] ?? '', 0, 5) ?> &middot;
                Dr. <?= htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) ?>
              </p>
            </div>
            <span class="badge badge-<?= $c['estado'] === 'pendiente' ? 'warning' : ($c['estado'] === 'confirmada' ? 'info' : ($c['estado'] === 'cancelada' ? 'danger' : 'success')) ?> text-[10px] flex-shrink-0 hidden sm:inline-flex">
              <?= ucfirst($c['estado']) ?>
            </span>
          </div>
          <div class="flex items-center gap-1 sm:flex-shrink-0 pl-16 sm:pl-0">
            <a href="<?= APP_URL ?>/paciente/comprobante?id=<?= $c['id'] ?>" class="btn btn-icon btn-xs btn-ghost text-sky-600" target="_blank" title="Comprobante">
              <i class="fas fa-print"></i>
            </a>
            <a href="<?= APP_URL ?>/paciente/reagendar?id=<?= $c['id'] ?>" class="btn btn-icon btn-xs btn-ghost text-amber-600" title="Reagendar">
              <i class="fas fa-exchange-alt"></i>
            </a>
            <?php if ($c['estado'] !== 'cancelada' && $c['estado'] !== 'atendida'): ?>
            <form method="POST" action="<?= APP_URL ?>/paciente/cancelar" onsubmit="return confirm('¿Cancelar esta cita?')">
              <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
              <input type="hidden" name="cita_id" value="<?= $c['id'] ?>">
              <button type="submit" class="btn btn-icon btn-xs btn-ghost text-red-500 hover:bg-red-50" title="Cancelar">
                <i class="fas fa-times"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Historial reciente -->
  <?php if (!empty($historial)): ?>
  <div class="card">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-sm font-bold text-slate-700">
        <i class="fas fa-history text-slate-400 mr-2"></i>Citas Anteriores
      </h2>
      <a href="<?= APP_URL ?>/paciente/historial" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition">
        Ver todo <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>
    <div class="divide-y divide-slate-50">
      <?php foreach ($historial as $c): ?>
      <div class="px-5 py-3 flex items-center gap-3 text-sm hover:bg-slate-50 transition">
        <span class="text-slate-400 text-xs font-mono w-16 flex-shrink-0"><?= date('d/m', strtotime($c['fecha'])) ?></span>
        <span class="text-slate-600 flex-1 truncate"><?= htmlspecialchars($c['especialidad']) ?></span>
        <span class="badge badge-<?= $c['estado'] === 'atendida' ? 'success' : 'secondary' ?> text-[10px] flex-shrink-0">
          <?= ucfirst($c['estado']) ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // ── Search ────────────────────────────────────────────────
  const searchInput = document.getElementById('searchCitas');
  const clearBtn = document.getElementById('clearSearch');
  const items = document.querySelectorAll('.cita-item');

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      clearBtn.classList.toggle('hidden', !q);

      items.forEach(function(item) {
        const text = item.getAttribute('data-search') || '';
        item.style.display = text.includes(q) ? '' : 'none';
      });
    });

    clearBtn.addEventListener('click', function() {
      searchInput.value = '';
      searchInput.dispatchEvent(new Event('input'));
      searchInput.focus();
    });
  }

  // ── Notifications ─────────────────────────────────────────
  const notifBtn = document.getElementById('notifBtn');
  const notifDropdown = document.getElementById('notifDropdown');
  const notifList = document.getElementById('notifList');
  const notifBadge = document.getElementById('notifBadge');
  const marcarLeidas = document.getElementById('marcarLeidas');

  function toggleNotif(e) {
    e.stopPropagation();
    notifDropdown.classList.toggle('hidden');
    if (!notifDropdown.classList.contains('hidden')) {
      fetchNotifs();
    }
  }

  function fetchNotifs() {
    fetch('<?= APP_URL ?>/paciente/notificaciones/json')
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (!res.success) return;
        var count = res.count || 0;
        var data = res.data || [];

        if (count > 0) {
          notifBadge.textContent = count;
          notifBadge.classList.remove('hidden');
        } else {
          notifBadge.classList.add('hidden');
        }

        if (data.length === 0) {
          notifList.innerHTML = '<div class="px-4 py-8 text-center text-slate-400 text-sm">Sin notificaciones</div>';
          return;
        }

        var html = '';
        data.forEach(function(n) {
          var icon = n.tipo === 'success' ? 'fa-check-circle text-green-500' :
                     n.tipo === 'error' ? 'fa-times-circle text-red-500' :
                     n.tipo === 'warning' ? 'fa-exclamation-circle text-amber-500' :
                     'fa-info-circle text-sky-500';
          html += '<div class="px-4 py-3 hover:bg-slate-50 transition">' +
                  '<div class="flex items-start gap-3">' +
                  '<i class="fas ' + icon + ' mt-0.5"></i>' +
                  '<div class="flex-1 min-w-0">' +
                  '<p class="text-sm font-semibold text-slate-700">' + escapeHtml(n.titulo) + '</p>' +
                  (n.mensaje ? '<p class="text-xs text-slate-400 mt-0.5">' + escapeHtml(n.mensaje) + '</p>' : '') +
                  '<p class="text-[10px] text-slate-300 mt-1">' + timeAgo(n.created_at) + '</p>' +
                  '</div></div></div>';
        });
        notifList.innerHTML = html;
      });
  }

  function escapeHtml(str) {
    if (!str) return '';
    var d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  function timeAgo(dateStr) {
    if (!dateStr) return '';
    var now = new Date();
    var d = new Date(dateStr.replace(' ', 'T'));
    var diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'ahora';
    if (diff < 3600) return Math.floor(diff / 60) + 'm';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h';
    return Math.floor(diff / 86400) + 'd';
  }

  if (notifBtn) {
    notifBtn.addEventListener('click', toggleNotif);
    document.addEventListener('click', function(e) {
      if (!notifDropdown.classList.contains('hidden') &&
          !notifDropdown.contains(e.target) &&
          e.target !== notifBtn && !notifBtn.contains(e.target)) {
        notifDropdown.classList.add('hidden');
      }
    });
  }

  if (marcarLeidas) {
    marcarLeidas.addEventListener('click', function() {
      fetch('<?= APP_URL ?>/paciente/notificaciones/marcar', { method: 'POST' })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          if (res.success) {
            notifBadge.classList.add('hidden');
            notifList.innerHTML = '<div class="px-4 py-8 text-center text-slate-400 text-sm">Sin notificaciones</div>';
          }
        });
    });
  }

  // Poll for new notifications every 30s
  setInterval(function() {
    fetch('<?= APP_URL ?>/paciente/notificaciones/json')
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.success && res.count > 0) {
          notifBadge.textContent = res.count;
          notifBadge.classList.remove('hidden');
        }
      });
  }, 30000);
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
