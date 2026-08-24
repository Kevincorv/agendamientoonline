<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Backups</h1>
    <p class="text-sm text-slate-400">Copias de seguridad de la base de datos</p>
  </div>
  <form method="POST" action="<?= APP_URL ?>/admin/backups/crear" class="inline">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <button type="submit" class="btn btn-primary" onclick="return confirm('¿Crear una copia de seguridad ahora?')">
      <i class="fas fa-database"></i> Crear Backup
    </button>
  </form>
</div>

<div class="card overflow-hidden fade-up">
  <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
    <p class="text-xs text-slate-400">
      <span class="font-semibold text-slate-600"><?= count($backups) ?></span> backups encontrados
    </p>
  </div>

  <?php if (empty($backups)): ?>
  <div class="p-12 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#e0f2fe">
      <i class="fas fa-database text-2xl" style="color:#0284c7"></i>
    </div>
    <h2 class="text-lg font-bold text-slate-700 mb-2">Sin Backups</h2>
    <p class="text-sm text-slate-400 max-w-md mx-auto">
      Aún no se han creado copias de seguridad. Hacé clic en "Crear Backup" para generar la primera.
    </p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Archivo</th>
          <th>Fecha</th>
          <th>Tamaño</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($backups as $b): ?>
        <tr>
          <td class="font-mono text-sm font-semibold text-slate-700"><?= htmlspecialchars($b['nombre']) ?></td>
          <td class="text-sm text-slate-500"><?= htmlspecialchars($b['fecha']) ?></td>
          <td class="text-sm text-slate-600">
            <?php
              $size = $b['tamano'];
              if ($size >= 1048576) {
                echo round($size / 1048576, 2) . ' MB';
              } elseif ($size >= 1024) {
                echo round($size / 1024, 1) . ' KB';
              } else {
                echo $size . ' B';
              }
            ?>
          </td>
          <td class="text-right">
            <div class="flex items-center justify-end gap-1">
              <a href="<?= APP_URL ?>/admin/backups/descargar?archivo=<?= urlencode($b['nombre']) ?>"
                 class="btn btn-icon btn-sm btn-ghost text-sky-600 hover:bg-sky-50" title="Descargar">
                <i class="fas fa-download"></i>
              </a>
              <form method="POST" action="<?= APP_URL ?>/admin/backups/restaurar"
                    onsubmit="return confirm('¿Restaurar este backup? Se reemplazarán todos los datos actuales.')"
                    class="inline">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="archivo" value="<?= htmlspecialchars($b['nombre']) ?>">
                <button type="submit" class="btn btn-icon btn-sm btn-ghost text-amber-600 hover:bg-amber-50" title="Restaurar">
                  <i class="fas fa-undo"></i>
                </button>
              </form>
              <form method="POST" action="<?= APP_URL ?>/admin/backups/eliminar"
                    onsubmit="return confirm('¿Eliminar este backup?')" class="inline">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="archivo" value="<?= htmlspecialchars($b['nombre']) ?>">
                <button type="submit" class="btn btn-icon btn-sm btn-ghost text-red-500 hover:bg-red-50" title="Eliminar">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-5 fade-up">
  <div class="flex items-start gap-3">
    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#fef3c7">
      <i class="fas fa-exclamation-triangle text-amber-500"></i>
    </div>
    <div>
      <p class="text-sm font-bold text-amber-800">Importante</p>
      <p class="text-xs text-amber-700 mt-0.5">
        Los backups se almacenan en el servidor. Se recomienda descargarlos periódicamente.
        La restauración reemplazará todos los datos actuales de la base de datos. Esta acción no se puede deshacer.
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
