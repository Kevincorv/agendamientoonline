<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Citas</h1>
    <p class="text-sm text-slate-400">Gestión de citas médicas</p>
  </div>
  <div class="flex items-center gap-2">
    <a href="<?= APP_URL ?>/admin/citas/exportar?<?= http_build_query($filtros) ?>" class="btn btn-secondary btn-sm">
      <i class="fas fa-file-export"></i> Exportar
    </a>
    <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
      <i class="fas fa-print"></i> Imprimir
    </button>
  </div>
</div>

<!-- Filtros -->
<form method="GET" action="<?= APP_URL ?>/admin/citas"
      class="card p-4 sm:p-5 mb-6 fade-up">
  <div class="flex flex-wrap gap-3 sm:gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Buscar paciente</label>
      <div class="search-wrap" style="max-width:100%">
        <i class="fas fa-search"></i>
        <input type="text" name="q" placeholder="Nombre o teléfono..."
               value="<?= htmlspecialchars($filtros['q'] ?? '') ?>"
               class="!pl-8 !bg-white">
      </div>
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Fecha</label>
      <input type="date" name="fecha" value="<?= htmlspecialchars($filtros['fecha'] ?? '') ?>"
             class="input-field">
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Estado</label>
      <select name="estado_id" class="input-field">
        <option value="">Todos</option>
        <option value="1" <?= ($filtros['estado_id'] ?? 0) == 1 ? 'selected' : '' ?>>Pendiente</option>
        <option value="2" <?= ($filtros['estado_id'] ?? 0) == 2 ? 'selected' : '' ?>>Confirmada</option>
        <option value="3" <?= ($filtros['estado_id'] ?? 0) == 3 ? 'selected' : '' ?>>Cancelada</option>
        <option value="4" <?= ($filtros['estado_id'] ?? 0) == 4 ? 'selected' : '' ?>>Atendida</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-search"></i> Filtrar
      </button>
      <a href="<?= APP_URL ?>/admin/citas" class="btn btn-secondary">
        <i class="fas fa-times"></i>
      </a>
    </div>
  </div>
</form>

<!-- Tabla -->
<div class="card overflow-hidden fade-up">
  <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
    <p class="text-xs text-slate-400">
      <span class="font-semibold text-slate-600"><?= $paginacion['total'] ?></span> citas encontradas
      <span class="hidden sm:inline">· Pág. <?= $paginacion['pagina'] ?>/<?= $paginacion['paginas'] ?></span>
    </p>
    <div class="flex items-center gap-2">
      <span class="text-xs text-slate-400">Mostrar</span>
      <select class="text-xs border border-slate-200 rounded-lg px-2 py-1 outline-none" onchange="cambiarPorPagina(this.value)">
        <option value="15" <?= ($paginacion['porPagina'] ?? 15) == 15 ? 'selected' : '' ?>>15</option>
        <option value="30" <?= ($paginacion['porPagina'] ?? 15) == 30 ? 'selected' : '' ?>>30</option>
        <option value="50" <?= ($paginacion['porPagina'] ?? 15) == 50 ? 'selected' : '' ?>>50</option>
      </select>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="data-table resp-table">
      <thead>
        <tr>
          <th>Paciente</th>
          <th>Médico / Especialidad</th>
          <th>Fecha / Hora</th>
          <th>Estado</th>
          <th>Cambiar estado</th>
          <th class="text-right">Acc.</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($citas)): ?>
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <i class="fas fa-calendar-times"></i>
              <h3>No se encontraron citas</h3>
              <p>Intenta cambiar los filtros de búsqueda</p>
            </div>
          </td>
        </tr>
        <?php else: foreach ($citas as $c): ?>
        <tr>
          <td data-label="Paciente">
            <div class="flex items-center gap-2.5">
              <div class="avatar avatar-sm"
                   style="background:linear-gradient(135deg,#0284c7,#0e7490)">
                <?= strtoupper(substr($c['nombre_paciente'], 0, 1)) ?>
              </div>
              <div>
                <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($c['nombre_paciente']) ?></p>
                <p class="text-slate-400 text-xs"><?= htmlspecialchars($c['telefono']) ?></p>
              </div>
            </div>
          </td>
          <td data-label="Médico">
            <p class="text-sm font-medium text-slate-700">
              Dr. <?= htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) ?>
            </p>
            <p class="text-xs text-slate-400"><?= htmlspecialchars($c['especialidad']) ?></p>
          </td>
          <td data-label="Fecha / Hora">
            <p class="text-sm text-slate-700 font-medium"><?= formatearFecha($c['fecha']) ?></p>
            <p class="font-mono text-xs text-slate-500"><?= htmlspecialchars(substr($c['hora'] ?? '', 0, 5)) ?></p>
          </td>
          <td data-label="Estado">
            <?php $type = $c['estado']; $text = ucfirst($c['estado']); ?>
            <?php require __DIR__ . '/../components/badge.php'; ?>
          </td>
          <td data-label="Cambiar estado">
            <select name="estado_id" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 outline-none focus:border-sky-500 status-select"
                    data-cita-id="<?= $c['id'] ?>"
                    data-csrf="<?= $csrfToken ?>"
                    style="min-width:100px">
              <option value="1" <?= $c['estado'] === 'pendiente'  ? 'selected' : '' ?>>Pendiente</option>
              <option value="2" <?= $c['estado'] === 'confirmada' ? 'selected' : '' ?>>Confirmar</option>
              <option value="3" <?= $c['estado'] === 'cancelada'  ? 'selected' : '' ?>>Cancelar</option>
              <option value="4" <?= $c['estado'] === 'atendida' ? 'selected' : '' ?>>Atender</option>
            </select>
          </td>
          <td data-label="Acc.">
            <div class="flex items-center justify-end gap-1">
              <button type="button" class="btn btn-icon btn-sm btn-ghost"
                      onclick="verDetalleCita(<?= $c['id'] ?>)"
                      title="Ver detalle">
                <i class="fas fa-eye"></i>
              </button>
              <button type="button" class="btn btn-icon btn-sm btn-ghost text-amber-600 hover:bg-amber-50"
                      onclick="abrirEditarCita(<?= $c['id'] ?>, <?= $c['medico_id'] ?>, '<?= $c['fecha'] ?>', '<?= $c['hora'] ?>')"
                      title="Editar cita — reasignar medico">
                <i class="fas fa-pen"></i>
              </button>
              <?php
                $paciente = htmlspecialchars($c['nombre_paciente'], ENT_QUOTES);
                $fecha    = formatearFecha($c['fecha']);
                $hora     = htmlspecialchars(substr($c['hora'] ?? '', 0, 5), ENT_QUOTES);
                $url      = APP_URL . '/admin/citas/' . $c['id'] . '/eliminar';
              ?>
              <button type="button" class="btn btn-icon btn-sm btn-ghost text-red-500 hover:bg-red-50"
                      onclick="openDeleteModal('<?= $url ?>', 'Se eliminará la cita de <?= $paciente ?> — <?= $fecha ?> a las <?= $hora ?>.')"
                      title="Eliminar">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginación -->
  <?php if ($paginacion['paginas'] > 1): ?>
  <div class="pagination flex-wrap">
    <p class="text-xs text-slate-400 mr-auto">
      Página <?= $paginacion['pagina'] ?> de <?= $paginacion['paginas'] ?>
    </p>
    <div class="flex gap-1">
      <?php
      $qs = $_GET;
      $rango = 2;
      $inicio = max(1, $paginacion['pagina'] - $rango);
      $final  = min($paginacion['paginas'], $paginacion['pagina'] + $rango);
      ?>
      <?php if ($paginacion['pagina'] > 1):
        $qs['pagina'] = 1; ?>
      <a href="<?= APP_URL ?>/admin/citas?<?= http_build_query($qs) ?>" class="page-btn">
        <i class="fas fa-angle-double-left"></i>
      </a>
      <?php endif; ?>

      <?php for ($p = $inicio; $p <= $final; $p++):
        $qs['pagina'] = $p; ?>
      <a href="<?= APP_URL ?>/admin/citas?<?= http_build_query($qs) ?>"
         class="page-btn <?= $p === $paginacion['pagina'] ? 'active' : '' ?>">
        <?= $p ?>
      </a>
      <?php endfor; ?>

      <?php if ($paginacion['pagina'] < $paginacion['paginas']):
        $qs['pagina'] = $paginacion['paginas']; ?>
      <a href="<?= APP_URL ?>/admin/citas?<?= http_build_query($qs) ?>" class="page-btn">
        <i class="fas fa-angle-double-right"></i>
      </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- ─── MODAL DETALLE ─── -->
<div id="detalleModal" class="modal-overlay" onclick="if(event.target===this)closeDetalleModal()">
  <div class="modal-box" style="max-width:500px">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="text-base font-extrabold text-slate-800">
        <i class="fas fa-calendar-check text-sky-500 mr-2"></i>Detalle de Cita
      </h3>
      <button type="button" onclick="closeDetalleModal()" class="text-slate-400 hover:text-slate-600 transition">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div id="detalleBody" class="p-5 space-y-3 text-sm">
      <div class="flex justify-center py-4">
        <div class="w-8 h-8 border-2 border-sky-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
    </div>
  </div>
</div>

<!-- ─── MODAL EDITAR CITA ─── -->
<div id="editarCitaModal" class="modal-overlay" onclick="if(event.target===this)cerrarEditarCita()">
  <div class="modal-box" style="max-width:480px">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h3 class="text-base font-extrabold text-slate-800">
        <i class="fas fa-pen text-amber-500 mr-2"></i>Reasignar Cita
      </h3>
      <button type="button" onclick="cerrarEditarCita()" class="text-slate-400 hover:text-slate-600 transition">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form id="editarCitaForm" class="p-5">
      <input type="hidden" name="cita_id" id="editCitaId">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <p class="text-xs text-slate-600 bg-amber-50 rounded-lg px-3 py-2 border border-amber-200 mb-4">
        <i class="fas fa-info-circle text-amber-500 mr-1"></i>
        Reasignar a otro medico. La cita volvera a estado <strong>pendiente</strong>.
      </p>

      <div class="mb-3">
        <label for="editMedicoId" class="block text-xs font-semibold text-slate-500 mb-1">Nuevo medico</label>
        <select name="medico_id" id="editMedicoId" required class="input-field">
          <option value="">Seleccionar medico</option>
          <?php foreach ($medicos as $m): ?>
          <option value="<?= $m['id'] ?>">Dr. <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="editFecha" class="block text-xs font-semibold text-slate-500 mb-1">Nueva fecha</label>
        <input type="date" name="fecha" id="editFecha" required class="input-field"
               min="<?= date('Y-m-d') ?>">
      </div>

      <div class="mb-3">
        <label for="editHora" class="block text-xs font-semibold text-slate-500 mb-1">Nueva hora</label>
        <select name="hora" id="editHora" required class="input-field">
          <option value="">Selecciona medico y fecha primero</option>
        </select>
      </div>

      <div id="editSlotStatus" class="text-xs hidden mb-3"></div>

      <button type="submit" class="btn btn-primary w-full" id="btnGuardarEdit">
        <i class="fas fa-save"></i> Guardar cambios
      </button>
    </form>
  </div>
</div>

<script>
function cambiarPorPagina(val) {
  var url = new URL(window.location.href);
  url.searchParams.set('porPagina', val);
  url.searchParams.set('pagina', '1');
  window.location.href = url.toString();
}

function verDetalleCita(id) {
  var modal = document.getElementById('detalleModal');
  var body  = document.getElementById('detalleBody');
  body.innerHTML = '<div class="flex justify-center py-4"><div class="w-8 h-8 border-2 border-sky-500 border-t-transparent rounded-full animate-spin"></div></div>';
  modal.classList.add('active');

  fetch('<?= APP_URL ?>/admin/api/citas/detalle?id=' + id)
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (!res.success) {
        body.innerHTML = '<p class="text-red-500 text-center py-4">' + res.message + '</p>';
        return;
      }
      var c = res.cita;
      var estados = {pendiente:'warning', confirmada:'info', cancelada:'danger', atendida:'success', 'no asistio':'secondary'};
      var badge = estados[c.estado] || 'secondary';
      body.innerHTML =
        '<div class="grid grid-cols-2 gap-3">' +
          '<div class="col-span-2 flex items-center gap-3 pb-3 border-b border-slate-100">' +
            '<div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background:linear-gradient(135deg,#0284c7,#0e7490)">' +
              c.nombre_paciente.charAt(0).toUpperCase() +
            '</div>' +
            '<div class="flex-1">' +
              '<p class="font-bold text-slate-800">' + escHtml(c.nombre_paciente) + '</p>' +
              '<p class="text-xs text-slate-400">' + escHtml(c.telefono || '') + '</p>' +
            '</div>' +
            '<span class="badge badge-' + badge + '">' + ucfirst(c.estado) + '</span>' +
          '</div>' +
          '<div class="col-span-2 grid grid-cols-2 gap-3">' +
            '<div class="bg-slate-50 rounded-lg p-3"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Especialidad</p><p class="font-semibold text-slate-700 text-sm">' + escHtml(c.especialidad || '—') + '</p></div>' +
            '<div class="bg-slate-50 rounded-lg p-3"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Médico</p><p class="font-semibold text-slate-700 text-sm">Dr. ' + escHtml(c.medico_nombre || '') + ' ' + escHtml(c.medico_apellido || '') + '</p></div>' +
            '<div class="bg-slate-50 rounded-lg p-3"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Fecha</p><p class="font-semibold text-slate-700 text-sm">' + formatFecha(c.fecha) + '</p></div>' +
            '<div class="bg-slate-50 rounded-lg p-3"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Hora</p><p class="font-semibold text-slate-700 text-sm font-mono">' + escHtml(c.hora) + '</p></div>' +
          '</div>';

      if (c.email) {
        body.innerHTML +=
          '<div class="bg-slate-50 rounded-lg p-3 col-span-2"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Email</p><p class="text-sm text-slate-700">' + escHtml(c.email) + '</p></div>';
      }

      if (c.motivo) {
        body.innerHTML +=
          '<div class="bg-slate-50 rounded-lg p-3 col-span-2"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Motivo</p><p class="text-sm text-slate-700">' + escHtml(c.motivo) + '</p></div>';
      }

      if (c.notas_medico) {
        body.innerHTML +=
          '<div class="bg-slate-50 rounded-lg p-3 col-span-2"><p class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Notas del médico</p><p class="text-sm text-slate-700">' + escHtml(c.notas_medico) + '</p></div>';
      }

      body.innerHTML += '</div>';
    })
    .catch(function() {
      body.innerHTML = '<p class="text-red-500 text-center py-4">Error al cargar el detalle.</p>';
    });
}

function closeDetalleModal() {
  document.getElementById('detalleModal').classList.remove('active');
}

function escHtml(s) {
  if (!s) return '';
  var d = document.createElement('div');
  d.textContent = s;
  return d.innerHTML;
}

function ucfirst(s) {
  if (!s) return '';
  return s.charAt(0).toUpperCase() + s.slice(1);
}

function formatFecha(f) {
  if (!f) return '—';
  var parts = f.split('-');
  if (parts.length !== 3) return f;
  return parts[2] + '/' + parts[1] + '/' + parts[0];
}

// ─── EDITAR CITA ───
function abrirEditarCita(id, medicoId, fecha, hora) {
  document.getElementById('editCitaId').value = id;
  document.getElementById('editMedicoId').value = medicoId || '';
  document.getElementById('editFecha').value = fecha || '';
  document.getElementById('editHora').innerHTML = '<option value="">Selecciona medico y fecha primero</option>';
  document.getElementById('editHora').value = '';
  document.getElementById('editSlotStatus').classList.add('hidden');
  document.getElementById('editarCitaModal').classList.add('active');
  document.body.style.overflow = 'hidden';
  if (medicoId && fecha) cargarSlotsEdit(medicoId, fecha);
}

function cerrarEditarCita() {
  document.getElementById('editarCitaModal').classList.remove('active');
  document.body.style.overflow = '';
}

document.getElementById('editMedicoId').addEventListener('change', function() {
  var medicoId = this.value;
  var fecha = document.getElementById('editFecha').value;
  if (medicoId && fecha) cargarSlotsEdit(medicoId, fecha);
});

document.getElementById('editFecha').addEventListener('change', function() {
  var medicoId = document.getElementById('editMedicoId').value;
  var fecha = this.value;
  if (medicoId && fecha) cargarSlotsEdit(medicoId, fecha);
});

function cargarSlotsEdit(medicoId, fecha) {
  var sel = document.getElementById('editHora');
  var status = document.getElementById('editSlotStatus');
  sel.innerHTML = '<option value="">Cargando horarios...</option>';
  sel.disabled = true;
  status.classList.add('hidden');

  fetch('<?= APP_URL ?>/admin/api/slots?medico_id=' + medicoId + '&fecha=' + fecha)
    .then(function(r) { return r.json(); })
    .then(function(res) {
      sel.innerHTML = '';
      if (!res.success || !res.slots || res.slots.length === 0) {
        sel.innerHTML = '<option value="">No hay horarios disponibles</option>';
        return;
      }
      var slots = res.slots;
      // Check if first slot has a mensaje (blocked)
      if (slots.length === 1 && slots[0].hora === null) {
        sel.innerHTML = '<option value="">' + escHtml(slots[0].mensaje || 'No disponible') + '</option>';
        return;
      }
      var hayDisponibles = false;
      slots.forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = s.hora;
        opt.textContent = s.hora;
        if (!s.disponible) {
          opt.disabled = true;
          opt.textContent += ' (ocupado)';
        } else {
          hayDisponibles = true;
        }
        sel.appendChild(opt);
      });
      if (!hayDisponibles) {
        sel.innerHTML = '<option value="">Todos los horarios estan ocupados</option>';
      }
      sel.disabled = false;
    })
    .catch(function() {
      sel.innerHTML = '<option value="">Error al cargar horarios</option>';
    });
}

document.getElementById('editarCitaForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var btn = document.getElementById('btnGuardarEdit');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span> Guardando...';

  var formData = new FormData(this);

  fetch('<?= APP_URL ?>/admin/api/citas/cambiar-medico', {
    method: 'POST',
    body: formData
  })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.success) {
        showToast('Cita reasignada correctamente', 'success');
        cerrarEditarCita();
        setTimeout(function() { location.reload(); }, 800);
      } else {
        showToast(res.message || 'Error al reasignar', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
      }
    })
    .catch(function() {
      showToast('Error de conexion', 'error');
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-save"></i> Guardar cambios';
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
