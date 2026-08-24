<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Feriados</h1>
    <p class="text-sm text-slate-400">Gestion de dias feriados — los slots no se generaran en estas fechas</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Formulario -->
  <div class="card p-5 fade-up lg:col-span-1">
    <h3 class="text-sm font-extrabold text-slate-800 mb-4">
      <i class="fas fa-plus-circle text-sky-500 mr-1.5"></i>Agregar feriado
    </h3>
    <form method="POST" action="<?= APP_URL ?>/admin/feriados/crear">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="space-y-4">
        <div class="floating-group">
          <input type="date" name="fecha" id="fechaFeriado" required
                 class="floating-input" placeholder=" ">
          <label for="fechaFeriado" class="floating-label">Fecha</label>
        </div>
        <div class="floating-group">
          <input type="text" name="motivo" id="motivoFeriado" required
                 class="floating-input" placeholder=" " maxlength="200">
          <label for="motivoFeriado" class="floating-label">Motivo</label>
        </div>
        <button type="submit" class="btn btn-primary w-full">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
    </form>
  </div>

  <!-- Listado -->
  <div class="card overflow-hidden fade-up lg:col-span-2">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
      <p class="text-xs text-slate-400">
        <span class="font-semibold text-slate-600"><?= count($feriados) ?></span> feriados registrados
      </p>
    </div>
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Motivo</th>
            <th>Estado</th>
            <th class="text-right">Acc.</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($feriados)): ?>
          <tr>
            <td colspan="4">
              <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No hay feriados registrados</h3>
                <p>Agrega feriados para bloquear la agenda automaticamente</p>
              </div>
            </td>
          </tr>
          <?php else: foreach ($feriados as $f): ?>
          <tr>
            <td data-label="Fecha">
              <span class="font-semibold text-slate-700 text-sm"><?= formatearFecha($f['fecha']) ?></span>
            </td>
            <td data-label="Motivo">
              <span class="text-sm text-slate-600"><?= htmlspecialchars($f['motivo']) ?></span>
            </td>
            <td data-label="Estado">
              <?php if ($f['activo']): ?>
              <span class="badge badge-green">Activo</span>
              <?php else: ?>
              <span class="badge badge-gray">Inactivo</span>
              <?php endif; ?>
            </td>
            <td data-label="Acc.">
              <div class="flex items-center justify-end gap-1">
                <form method="POST" action="<?= APP_URL ?>/admin/feriados/toggle" class="inline">
                  <input type="hidden" name="id" value="<?= $f['id'] ?>">
                  <button type="submit" class="btn btn-icon btn-sm btn-ghost
                    <?= $f['activo'] ? 'text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:bg-slate-100' ?>"
                    title="<?= $f['activo'] ? 'Desactivar' : 'Activar' ?>">
                    <i class="fas <?= $f['activo'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                  </button>
                </form>
                <form method="POST" action="<?= APP_URL ?>/admin/feriados/eliminar"
                      onsubmit="return confirm('Eliminar este feriado?')">
                  <input type="hidden" name="id" value="<?= $f['id'] ?>">
                  <button type="submit" class="btn btn-icon btn-sm btn-ghost text-red-500 hover:bg-red-50" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
