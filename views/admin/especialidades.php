<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Especialidades</h1>
    <p class="text-sm text-slate-400">Gestión de especialidades médicas</p>
  </div>
  <button type="button" class="btn btn-primary"
          onclick="document.getElementById('modalCrearEsp').classList.add('active')">
    <i class="fas fa-plus"></i> Nueva especialidad
  </button>
</div>

<!-- Grid de especialidades -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
  <?php if (empty($especialidades)):
    $icon = 'fa-stethoscope'; $message = 'No hay especialidades registradas';
    require __DIR__ . '/../components/table_empty.php';
  else:
    $espColors = ['#0284c7','#7c3aed','#0891b2','#059669','#d97706','#dc2626','#4f46e5','#db2777'];
    $i = 0;
    foreach ($especialidades as $e):
      $color = $espColors[$i % count($espColors)];
      $i++; ?>
  <div class="card p-5 fade-up relative group hover:shadow-lg transition-all duration-200">
    <!-- Color accent bar -->
    <div class="absolute top-0 left-4 right-4 h-1 rounded-full" style="background:<?= $color ?>;opacity:.4"></div>

    <div class="flex items-center gap-3 mb-4 mt-1">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white text-lg"
           style="background:<?= $color ?>">
        <i class="fas <?= htmlspecialchars($e['icono'] ?? 'fa-stethoscope') ?>"></i>
      </div>
      <div class="min-w-0 flex-1">
        <p class="font-bold text-slate-800 text-sm truncate"><?= htmlspecialchars($e['nombre']) ?></p>
      </div>
    </div>

    <p class="text-xs text-slate-500 mb-4 line-clamp-2">
      <?= htmlspecialchars(mb_strimwidth($e['descripcion'] ?? 'Sin descripción', 0, 100, '...')) ?>
    </p>

    <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
      <button type="button"
              class="btn btn-sm btn-ghost flex-1"
              data-edit-id="<?= $e['id'] ?>"
              data-edit-nombre="<?= htmlspecialchars($e['nombre'], ENT_QUOTES) ?>"
              data-edit-descripcion="<?= htmlspecialchars($e['descripcion'] ?? '', ENT_QUOTES) ?>"
              onclick="openEditEsp(this)">
        <i class="fas fa-pen text-sky-500"></i> Editar
      </button>
      <?php
        $nombreEsp = htmlspecialchars($e['nombre'], ENT_QUOTES);
        $urlEsp    = APP_URL . '/admin/especialidades/' . $e['id'] . '/eliminar';
      ?>
      <button type="button" class="btn btn-sm btn-danger flex-1"
              onclick="openDeleteModal('<?= $urlEsp ?>', 'Se eliminará la especialidad «<?= $nombreEsp ?>».')">
        <i class="fas fa-trash"></i> Eliminar
      </button>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>

<!-- Modal crear especialidad -->
<div id="modalCrearEsp" class="modal-overlay">
  <div class="modal-box" style="max-width:440px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-base font-extrabold text-slate-800">Nueva especialidad</h3>
      <button type="button" onclick="this.closest('.modal-overlay').classList.remove('active')"
              class="text-slate-400 hover:text-slate-600 transition text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/admin/especialidades/crear" class="flex flex-col gap-4" data-loading>
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="floating-group">
        <input type="text" name="nombre" class="input-field" placeholder=" " required>
        <label>Nombre de la especialidad</label>
      </div>
      <div class="floating-group">
        <textarea name="descripcion" rows="3" class="input-field" placeholder=" "></textarea>
        <label>Descripción</label>
      </div>
      <div class="flex justify-end gap-3 pt-1">
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('modalCrearEsp').classList.remove('active')">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal editar especialidad -->
<div id="modalEditEsp" class="modal-overlay">
  <div class="modal-box" style="max-width:440px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-base font-extrabold text-slate-800">Editar especialidad</h3>
      <button type="button" onclick="this.closest('.modal-overlay').classList.remove('active')"
              class="text-slate-400 hover:text-slate-600 transition text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form method="POST" action="" class="flex flex-col gap-4" data-loading id="formEditEsp">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input type="hidden" name="id" id="editEspId" value="">
      <div class="floating-group">
        <input type="text" name="nombre" id="editEspNombre" class="input-field" placeholder=" " required>
        <label>Nombre de la especialidad</label>
      </div>
      <div class="floating-group">
        <textarea name="descripcion" id="editEspDesc" rows="3" class="input-field" placeholder=" "></textarea>
        <label>Descripción</label>
      </div>
      <div class="flex justify-end gap-3 pt-1">
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('modalEditEsp').classList.remove('active')">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditEsp(btn) {
  document.getElementById('editEspId').value       = btn.getAttribute('data-edit-id');
  document.getElementById('editEspNombre').value   = btn.getAttribute('data-edit-nombre');
  document.getElementById('editEspDesc').value     = btn.getAttribute('data-edit-descripcion');
  var action = '<?= APP_URL ?>/admin/especialidades/' + btn.getAttribute('data-edit-id') + '/editar';
  document.getElementById('formEditEsp').action = action;
  document.getElementById('modalEditEsp').classList.add('active');
}
</script>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
