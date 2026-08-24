/* ═══════════════════════════════════════════════════════
   CITAS MÉDICAS ONLINE USF CORATEÍ — App JS v2.0
   Modern SaaS Interactions
   ═══════════════════════════════════════════════════════ */

// ─── Toast system ───
function showToast(message, type) {
  if (typeof type === 'undefined') type = 'success';
  var container = document.getElementById('toastContainer');
  if (!container) return;
  var toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  var icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
  toast.innerHTML = '<i class="fas ' + icon + '"></i>' + message;
  container.appendChild(toast);
  setTimeout(function () {
    toast.classList.add('removing');
    setTimeout(function () { toast.remove(); }, 260);
  }, 4000);
}

// ─── Mobile drawer ───
function openDrawer() {
  var el = document.getElementById('mobileDrawer');
  if (!el) return;
  el.classList.add('open');
  document.getElementById('drawerOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeDrawer() {
  var el = document.getElementById('mobileDrawer');
  if (!el) return;
  el.classList.remove('open');
  document.getElementById('drawerOverlay').classList.remove('active');
  document.body.style.overflow = '';
}

// ─── Delete confirmation modal ───
function openDeleteModal(action, text) {
  if (typeof text === 'undefined') text = 'Esta acci\u00f3n no se puede deshacer.';
  var modal = document.getElementById('deleteModal');
  var form = document.getElementById('deleteModalForm');
  var label = document.getElementById('deleteModalText');
  if (!modal || !form || !label) return;
  form.action = action;
  label.textContent = text;
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
  var modal = document.getElementById('deleteModal');
  if (!modal) return;
  modal.classList.remove('active');
  document.body.style.overflow = '';
}

// ─── Sidebar toggle ───
function toggleSidebar() {
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebar-overlay');
  if (!sidebar || !overlay) return;
  sidebar.classList.toggle('visible');
  overlay.classList.toggle('active');
  document.body.style.overflow = sidebar.classList.contains('visible') ? 'hidden' : '';
}

// ─── Dropdown toggle ───
document.addEventListener('click', function (e) {
  // Close all dropdowns if clicking outside
  var dropdowns = document.querySelectorAll('.dropdown-menu.open');
  dropdowns.forEach(function (m) {
    if (!m.closest('.dropdown') || !e.target.closest('.dropdown')) {
      m.classList.remove('open');
    }
  });
  // Close notification dropdown
  var notif = document.getElementById('notifDropdown');
  if (notif && notif.classList.contains('open') && !e.target.closest('.relative')) {
    notif.classList.remove('open');
  }
});

function toggleDropdown(btn) {
  var menu = btn.closest('.dropdown').querySelector('.dropdown-menu');
  if (!menu) return;
  menu.classList.toggle('open');
}

function toggleNotif() {
  var menu = document.getElementById('notifDropdown');
  if (!menu) return;
  menu.classList.toggle('open');
}

// ─── Back to top ───
(function () {
  var btn = document.createElement('button');
  btn.className = 'fab no-print';
  btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  btn.setAttribute('aria-label', 'Volver arriba');
  btn.onclick = function () { window.scrollTo({ top: 0, behavior: 'smooth' }); };
  document.body.appendChild(btn);
  window.addEventListener('scroll', function () {
    btn.style.display = window.scrollY > 400 ? 'flex' : 'none';
  });
  btn.style.display = 'none';
})();

// ─── Scroll reveal ───
(function () {
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-up');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });
  });
})();

// ─── Ripple effect on buttons ───
document.addEventListener('click', function (e) {
  var btn = e.target.closest('.btn-primary, .btn, .quick-action');
  if (!btn) return;
  var rect = btn.getBoundingClientRect();
  var ripple = document.createElement('span');
  ripple.className = 'ripple-effect';
  var size = Math.max(rect.width, rect.height);
  ripple.style.width = ripple.style.height = size + 'px';
  ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
  ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
  btn.style.position = 'relative';
  btn.style.overflow = 'hidden';
  btn.appendChild(ripple);
  setTimeout(function () { ripple.remove(); }, 600);
});

// ─── Modal ESC + click outside ───
document.addEventListener('click', function (e) {
  var modal = document.getElementById('deleteModal');
  if (modal && e.target === modal) closeDeleteModal();
  // Close any modal overlay on overlay click
  document.querySelectorAll('.modal-overlay.active').forEach(function (m) {
    if (e.target === m) m.classList.remove('active');
  });
});
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeDeleteModal();
    document.querySelectorAll('.modal-overlay.active').forEach(function (m) {
      m.classList.remove('active');
    });
    document.querySelectorAll('.dropdown-menu.open').forEach(function (m) {
      m.classList.remove('open');
    });
  }
});

// ─── Close sidebar on link click (mobile) ───
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('#sidebar a[href]').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth < 768) {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebar-overlay');
        if (sidebar) sidebar.classList.remove('visible');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });
});

// ─── Estado map ───
var estadoMap = {
  '1': { text: 'Pendiente',  type: 'pendiente' },
  '2': { text: 'Confirmada', type: 'confirmada' },
  '3': { text: 'Cancelada',  type: 'cancelada' },
  '4': { text: 'Atendida',   type: 'atendida' },
};
var estadoBadgeClass = {
  '1': 'badge-yellow',
  '2': 'badge-green',
  '3': 'badge-red',
  '4': 'badge-blue',
};

// ─── Cambio de estado vía AJAX ───
document.addEventListener('change', function (e) {
  if (!e.target.classList.contains('status-select')) return;
  var select = e.target;
  var citaId = select.getAttribute('data-cita-id');
  var csrf = select.getAttribute('data-csrf');
  var estadoId = select.value;

  var row = select.closest('tr');
  var badgeCell = row ? row.querySelector('[data-label="Estado"]') : null;

  fetch('/clinica-san-luis/admin/api/citas/estado', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'csrf_token=' + encodeURIComponent(csrf) + '&cita_id=' + encodeURIComponent(citaId) + '&estado_id=' + encodeURIComponent(estadoId)
  })
  .then(function (r) { return r.json(); })
  .then(function (data) {
    if (data.success) {
      showToast('Estado actualizado correctamente', 'success');
      if (badgeCell) {
        var info = estadoMap[estadoId] || { text: 'Desconocido' };
        var cls = estadoBadgeClass[estadoId] || 'badge-gray';
        badgeCell.innerHTML = '<span class="badge ' + cls + '">' + info.text + '</span>';
      }
      refrescarKPIs();
    } else {
      showToast(data.message || 'Error al actualizar', 'error');
    }
  })
  .catch(function () {
    showToast('Error de conexi\u00f3n', 'error');
  });
});

// ─── Refrescar KPIs ───
function refrescarKPIs() {
  var cards = document.querySelectorAll('.kpi-value');
  if (!cards.length) return;
  fetch('/clinica-san-luis/admin/api/stats/hoy')
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.success) return;
      var s = data.stats;
      var map = {
        'kpi-total':       s.total,
        'kpi-pendientes':  s.pendientes,
        'kpi-confirmadas': s.confirmadas,
        'kpi-canceladas':  s.canceladas,
      };
      Object.keys(map).forEach(function (id) {
        var el = document.querySelector('#' + id + ' .kpi-value');
        if (el) el.textContent = map[id];
      });
    })
    .catch(function () {});
}

// ─── Loading state on form submit ───
document.addEventListener('submit', function (e) {
  var form = e.target;
  if (!form.hasAttribute('data-loading')) return;
  var btn = form.querySelector('[type="submit"]');
  if (!btn || btn.disabled) { e.preventDefault(); return; }
  btn.disabled = true;
  btn.dataset.origHtml = btn.innerHTML;
  btn.innerHTML = '<span class="spinner" style="display:inline-block;vertical-align:middle;margin-right:6px;width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite"></span> Procesando...';
});

// ─── Client-side validation ───
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var firstError = null;
      form.querySelectorAll('[required]').forEach(function (input) {
        if (!input.value.trim()) {
          input.style.borderColor = '#dc2626';
          if (!firstError) firstError = input;
        } else {
          input.style.borderColor = '';
        }
      });
      if (firstError) {
        e.preventDefault();
        firstError.focus();
        showToast('Complet\u00e1 todos los campos obligatorios', 'error');
      }
    });
  });
});

// ─── Global search ───
function performSearch(query) {
  var searchable = window.location.pathname;
  if (searchable.includes('/admin/citas')) {
    var url = new URL(window.location.href);
    url.searchParams.set('q', query);
    url.searchParams.set('pagina', '1');
    window.location.href = url.toString();
  } else if (searchable.includes('/admin/medicos')) {
    var input = document.getElementById('searchMedico');
    if (input) { input.value = query; input.dispatchEvent(new Event('input')); }
  } else {
    showToast('Buscando: ' + query, 'info');
  }
}

// ─── Spinner keyframes ───
(function () {
  var style = document.createElement('style');
  style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
  document.head.appendChild(style);
})();
