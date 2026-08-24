<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Médicos</h1>
    <p class="text-sm text-slate-400">Gestión del cuerpo médico</p>
  </div>
  <button type="button" class="btn btn-primary"
          onclick="document.getElementById('modalCrearMedico').classList.add('active')">
    <i class="fas fa-plus"></i> Nuevo médico
  </button>
</div>

<!-- Filtros -->
<div class="card p-4 sm:p-5 mb-6 fade-up">
  <div class="flex flex-wrap gap-3 items-center">
    <div class="search-wrap flex-1 min-w-[200px]" style="max-width:100%">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Buscar médico por nombre, apellido o especialidad..." id="searchMedico"
             class="!pl-8 !bg-white">
    </div>
    <select id="filterEsp" class="input-field" style="width:auto;min-width:160px">
      <option value="">Todas las especialidades</option>
      <?php foreach ($especialidades as $e): ?>
      <option value="<?= htmlspecialchars($e['nombre']) ?>"><?= htmlspecialchars($e['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="filterDisponible" class="input-field" style="width:auto;min-width:140px">
      <option value="">Todos</option>
      <option value="1">Disponibles</option>
      <option value="0">No disponibles</option>
    </select>
  </div>
</div>

<!-- Grid de médicos -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="medicosGrid">
  <?php if (empty($medicos)):
    $icon = 'fa-user-md'; $message = 'No hay médicos registrados';
    require __DIR__ . '/../components/table_empty.php';
  else: foreach ($medicos as $m): ?>
  <div class="card p-5 fade-up medico-card"
       data-nombre="<?= strtolower(htmlspecialchars($m['nombre'] . ' ' . $m['apellido'])) ?>"
       data-especialidad="<?= strtolower(htmlspecialchars($m['especialidad'] ?? '')) ?>"
       data-disponible="<?= $m['disponible'] ?>">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center gap-3">
        <div class="avatar avatar-lg"
             style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
          <?= strtoupper(substr($m['nombre'], 0, 1) . substr($m['apellido'], 0, 1)) ?>
        </div>
        <div>
          <p class="font-bold text-slate-800 text-sm">Dr. <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?></p>
          <p class="text-xs text-slate-400"><?= htmlspecialchars($m['matricula'] ?? 'Sin matrícula') ?></p>
        </div>
      </div>
      <form method="POST" action="<?= APP_URL ?>/admin/medicos/disponibilidad" class="flex-shrink-0">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="id" value="<?= $m['id'] ?>">
        <button type="submit"
                class="btn btn-sm <?= $m['disponible'] ? 'btn-success' : 'btn-secondary' ?>">
          <?= $m['disponible'] ? 'Activo' : 'Inactivo' ?>
        </button>
      </form>
    </div>

    <div class="flex flex-wrap gap-2 mb-4">
      <span class="badge badge-sky"><i class="fas fa-stethoscope mr-1"></i><?= htmlspecialchars($m['especialidad'] ?? 'General') ?></span>
      <span class="badge <?= $m['disponible'] ? 'badge-green' : 'badge-red' ?>">
        <span class="badge-dot <?= $m['disponible'] ? 'green' : 'red' ?> mr-1"></span>
        <?= $m['disponible'] ? 'Disponible' : 'No disponible' ?>
      </span>
    </div>

    <div class="space-y-1.5 text-xs text-slate-500 mb-4">
      <p><i class="fas fa-phone w-4 text-slate-400 mr-1"></i> <?= htmlspecialchars($m['telefono'] ?? '—') ?></p>
    </div>

    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
      <button type="button"
              class="btn btn-sm btn-ghost flex-1"
              data-edit-id="<?= $m['id'] ?>"
              data-edit-nombre="<?= htmlspecialchars($m['nombre'], ENT_QUOTES) ?>"
              data-edit-apellido="<?= htmlspecialchars($m['apellido'], ENT_QUOTES) ?>"
              data-edit-email="<?= htmlspecialchars($m['email'] ?? '', ENT_QUOTES) ?>"
              data-edit-telefono="<?= htmlspecialchars($m['telefono'] ?? '', ENT_QUOTES) ?>"
              data-edit-matricula="<?= htmlspecialchars($m['matricula'] ?? '', ENT_QUOTES) ?>"
              data-edit-especialidad_id="<?= $m['especialidad_id'] ?>"
              data-edit-descripcion="<?= htmlspecialchars($m['descripcion'] ?? '', ENT_QUOTES) ?>"
              onclick="openEditMed(this)">
        <i class="fas fa-pen text-sky-500"></i> Editar
      </button>
      <?php
        $nombreMedico = htmlspecialchars($m['nombre'] . ' ' . $m['apellido'], ENT_QUOTES);
        $urlMedico    = APP_URL . '/admin/medicos/' . $m['id'] . '/eliminar';
      ?>
      <button type="button" class="btn btn-sm btn-danger flex-1"
              onclick="openDeleteModal('<?= $urlMedico ?>', 'Se eliminará al Dr. <?= $nombreMedico ?> y todos sus datos asociados.')">
        <i class="fas fa-trash"></i> Eliminar
      </button>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>

<!-- Modal crear médico -->
<div id="modalCrearMedico" class="modal-overlay">
  <div class="modal-box" style="max-width:520px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-base font-extrabold text-slate-800">Nuevo médico</h3>
      <button type="button" onclick="this.closest('.modal-overlay').classList.remove('active')"
              class="text-slate-400 hover:text-slate-600 transition text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/admin/medicos/crear" class="grid grid-cols-2 gap-4" data-loading>
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="floating-group">
        <input type="text" name="nombre" class="input-field" placeholder=" " required>
        <label>Nombre</label>
      </div>
      <div class="floating-group">
        <input type="text" name="apellido" class="input-field" placeholder=" " required>
        <label>Apellido</label>
      </div>
      <div class="floating-group">
        <select name="especialidad_id" class="input-field" required>
          <option value="">Seleccioná...</option>
          <?php foreach ($especialidades as $e): ?>
          <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="floating-group">
        <input type="text" name="matricula" class="input-field" placeholder=" ">
        <label>Matrícula</label>
      </div>
      <div class="floating-group">
        <input type="email" name="email" class="input-field" placeholder=" ">
        <label>Email</label>
      </div>
      <div class="floating-group">
        <input type="text" name="telefono" class="input-field" placeholder=" ">
        <label>Teléfono</label>
      </div>
      <div class="col-span-2 flex justify-end gap-3 pt-2">
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('modalCrearMedico').classList.remove('active')">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal editar médico -->
<div id="modalEditMedico" class="modal-overlay">
  <div class="modal-box" style="max-width:520px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-base font-extrabold text-slate-800">Editar médico</h3>
      <button type="button" onclick="this.closest('.modal-overlay').classList.remove('active')"
              class="text-slate-400 hover:text-slate-600 transition text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form method="POST" action="" class="grid grid-cols-2 gap-4" data-loading id="formEditMedico">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input type="hidden" name="id" id="editMedId" value="">
      <div class="floating-group">
        <input type="text" name="nombre" id="editMedNombre" class="input-field" placeholder=" " required>
        <label>Nombre</label>
      </div>
      <div class="floating-group">
        <input type="text" name="apellido" id="editMedApellido" class="input-field" placeholder=" " required>
        <label>Apellido</label>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5">Especialidad</label>
        <select name="especialidad_id" id="editMedEsp" class="input-field" required>
          <option value="">Seleccioná...</option>
          <?php foreach ($especialidades as $e): ?>
          <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="floating-group">
        <input type="text" name="matricula" id="editMedMatricula" class="input-field" placeholder=" ">
        <label>Matrícula</label>
      </div>
      <div class="floating-group">
        <input type="email" name="email" id="editMedEmail" class="input-field" placeholder=" ">
        <label>Email</label>
      </div>
      <div class="floating-group">
        <input type="text" name="telefono" id="editMedTelefono" class="input-field" placeholder=" ">
        <label>Teléfono</label>
      </div>
      <div class="col-span-2 flex justify-end gap-3 pt-2">
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('modalEditMedico').classList.remove('active')">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditMed(btn) {
  document.getElementById('editMedId').value          = btn.getAttribute('data-edit-id');
  document.getElementById('editMedNombre').value      = btn.getAttribute('data-edit-nombre');
  document.getElementById('editMedApellido').value    = btn.getAttribute('data-edit-apellido');
  document.getElementById('editMedEmail').value        = btn.getAttribute('data-edit-email');
  document.getElementById('editMedTelefono').value     = btn.getAttribute('data-edit-telefono');
  document.getElementById('editMedMatricula').value    = btn.getAttribute('data-edit-matricula');
  document.getElementById('editMedEsp').value          = btn.getAttribute('data-edit-especialidad_id');
  var action = '<?= APP_URL ?>/admin/medicos/' + btn.getAttribute('data-edit-id') + '/editar';
  document.getElementById('formEditMedico').action = action;
  document.getElementById('modalEditMedico').classList.add('active');
}

// Search & filter
document.addEventListener('DOMContentLoaded', function() {
  var search = document.getElementById('searchMedico');
  var filterEsp = document.getElementById('filterEsp');
  var filterDispo = document.getElementById('filterDisponible');
  if (!search) return;

  function filtrar() {
    var q = search.value.toLowerCase().trim();
    var esp = filterEsp.value.toLowerCase();
    var disp = filterDispo.value;
    document.querySelectorAll('.medico-card').forEach(function(card) {
      var nombre = card.getAttribute('data-nombre') || '';
      var especialidad = card.getAttribute('data-especialidad') || '';
      var disponible = card.getAttribute('data-disponible');
      var matchNombre = !q || nombre.includes(q) || especialidad.includes(q);
      var matchEsp = !esp || especialidad === esp;
      var matchDisp = disp === '' || disponible === disp;
      card.style.display = (matchNombre && matchEsp && matchDisp) ? '' : 'none';
    });
  }

  search.addEventListener('input', filtrar);
  filterEsp.addEventListener('change', filtrar);
  filterDispo.addEventListener('change', filtrar);
});
</script>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
