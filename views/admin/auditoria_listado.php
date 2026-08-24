<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Auditoría</h1>
    <p class="text-sm text-slate-400">Registro de actividades del sistema</p>
  </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 mb-5 fade-up">
  <form method="GET" action="<?= APP_URL ?>/admin/auditoria" class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1">Acción</label>
      <select name="accion" class="input-field text-xs" style="width:140px">
        <option value="">Todas</option>
        <?php foreach (['login','logout','login_fallido','crear','editar','eliminar'] as $a): ?>
        <option value="<?= $a ?>" <?= (($_GET['accion'] ?? '') === $a) ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$a)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1">Tabla</label>
      <select name="tabla" class="input-field text-xs" style="width:140px">
        <option value="">Todas</option>
        <?php foreach (['usuarios','citas','medicos','especialidades'] as $t): ?>
        <option value="<?= $t ?>" <?= (($_GET['tabla'] ?? '') === $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1">Desde</label>
      <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>" class="input-field text-xs" style="width:140px">
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1">Hasta</label>
      <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>" class="input-field text-xs" style="width:140px">
    </div>
    <div class="flex gap-2">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrar</button>
      <a href="<?= APP_URL ?>/admin/auditoria" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
    </div>
  </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden fade-up">
  <div class="overflow-x-auto">
    <table class="w-full data-table resp-table">
      <thead>
        <tr>
          <th>Fecha / Hora</th>
          <th>Usuario</th>
          <th>Acción</th>
          <th>Tabla</th>
          <th>IP</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($resultado['datos'])): ?>
        <tr>
          <td colspan="6" class="text-center py-12">
            <div class="text-slate-300 text-3xl mb-2"><i class="fas fa-history"></i></div>
            <p class="text-sm font-semibold text-slate-500">Sin registros de auditoría</p>
            <p class="text-xs text-slate-400 mt-0.5">No hay actividades registradas con los filtros seleccionados</p>
          </td>
        </tr>
        <?php else: foreach ($resultado['datos'] as $log): ?>
        <tr>
          <td data-label="Fecha">
            <span class="text-sm font-medium text-slate-700 whitespace-nowrap"><?= date('d/m/Y', strtotime($log['created_at'])) ?></span>
            <span class="text-xs text-slate-400 font-mono block"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
          </td>
          <td data-label="Usuario">
            <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($log['usuario_nombre'] ?? '—') ?></span>
          </td>
          <td data-label="Acción">
            <?php
              $colorMap = [
                'login' => 'badge-green', 'logout' => 'badge-gray',
                'login_fallido' => 'badge-yellow', 'crear' => 'badge-blue',
                'editar' => 'badge-yellow', 'eliminar' => 'badge-red',
              ];
              $bc = $colorMap[$log['accion']] ?? 'badge-gray';
            ?>
            <span class="badge <?= $bc ?>"><?= ucfirst(str_replace('_', ' ', $log['accion'])) ?></span>
          </td>
          <td data-label="Tabla">
            <?php if ($log['tabla']): ?>
              <span class="text-sm font-medium text-slate-600"><?= htmlspecialchars(ucfirst($log['tabla'])) ?></span>
              <?php if ($log['registro_id']): ?>
                <span class="text-xs text-slate-300">#<?= $log['registro_id'] ?></span>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-xs text-slate-300">—</span>
            <?php endif; ?>
          </td>
          <td data-label="IP">
            <span class="font-mono text-xs text-slate-500"><?= htmlspecialchars($log['ip'] ?? '—') ?></span>
          </td>
          <td data-label="Descripción">
            <p class="text-sm text-slate-600 truncate max-w-[200px]" title="<?= htmlspecialchars($log['descripcion'] ?? '') ?>">
              <?= htmlspecialchars($log['descripcion'] ?? '—') ?>
            </p>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginación -->
  <?php if ($resultado['paginas'] > 1): ?>
  <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100 flex-wrap gap-3">
    <p class="text-xs text-slate-400">
      Página <?= $resultado['pagina'] ?> de <?= $resultado['paginas'] ?>
      (<?= $resultado['total'] ?> registros)
    </p>
    <div class="flex gap-1">
      <?php
      $qs = $_GET;
      $rango = 2;
      $inicio = max(1, $resultado['pagina'] - $rango);
      $final  = min($resultado['paginas'], $resultado['pagina'] + $rango);
      for ($p = $inicio; $p <= $final; $p++):
        $qs['pagina'] = $p; ?>
      <a href="<?= APP_URL ?>/admin/auditoria?<?= http_build_query($qs) ?>"
         class="page-btn <?= $p === $resultado['pagina'] ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
