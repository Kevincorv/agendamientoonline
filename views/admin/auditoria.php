<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<?php
$acciones = ['login','logout','login_fallido','cuenta_bloqueada','login_bloqueado',
             'crear','editar','eliminar','acceso_denegado'];
$tablas   = ['usuarios','citas','medicos','especialidades','pacientes',''];

$accionColors = [
    'login'            => 'badge-green',
    'logout'           => 'badge-gray',
    'login_fallido'    => 'badge-yellow',
    'cuenta_bloqueada' => 'badge-red',
    'login_bloqueado'  => 'badge-red',
    'crear'            => 'badge-blue',
    'editar'           => 'badge-yellow',
    'eliminar'         => 'badge-red',
    'acceso_denegado'  => 'badge-red',
];
?>

<div class="card fade-up">
  <!-- Filtros -->
  <div class="px-6 py-4 border-b border-slate-100">
    <form method="GET" action="<?= APP_URL ?>/admin/auditoria" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Acción</label>
        <select name="accion" class="input-field text-xs py-2 w-36">
          <option value="">Todas</option>
          <?php foreach ($acciones as $a): ?>
          <option value="<?= $a ?>" <?= (($_GET['accion'] ?? '') === $a) ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$a)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Tabla</label>
        <select name="tabla" class="input-field text-xs py-2 w-36">
          <option value="">Todas</option>
          <?php foreach ($tablas as $t): if(!$t) continue; ?>
          <option value="<?= $t ?>" <?= (($_GET['tabla'] ?? '') === $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Desde</label>
        <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>" class="input-field text-xs py-2 w-36">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Hasta</label>
        <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>" class="input-field text-xs py-2 w-36">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">IP</label>
        <input type="text" name="ip" value="<?= htmlspecialchars($_GET['ip'] ?? '') ?>" placeholder="192.168.1.1" class="input-field text-xs py-2 w-32">
      </div>
      <button type="submit" class="btn-primary text-xs py-2 px-4"><i class="fas fa-filter"></i>Filtrar</button>
      <a href="<?= APP_URL ?>/admin/auditoria" class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 text-slate-500 text-xs font-semibold hover:bg-slate-200 transition">
        <i class="fas fa-times text-xs"></i>Limpiar
      </a>
    </form>
  </div>

  <!-- Tabla -->
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead>
        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <th class="px-5 py-3 text-left font-semibold">Fecha / Hora</th>
          <th class="px-5 py-3 text-left font-semibold">Usuario</th>
          <th class="px-5 py-3 text-left font-semibold">Acción</th>
          <th class="px-5 py-3 text-left font-semibold">Tabla / ID</th>
          <th class="px-5 py-3 text-left font-semibold">IP</th>
          <th class="px-5 py-3 text-left font-semibold">Descripción</th>
          <th class="px-5 py-3 text-left font-semibold"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php if (empty($resultado['datos'])): ?>
        <tr>
          <td colspan="7" class="px-5 py-12 text-center text-slate-400">
            <i class="fas fa-shield-alt text-3xl mb-3 block opacity-20"></i>
            No hay registros de auditoría con estos filtros
          </td>
        </tr>
        <?php else: foreach ($resultado['datos'] as $log): ?>
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3 text-xs text-slate-500 whitespace-nowrap">
            <?= date('d/m/Y', strtotime($log['created_at'])) ?><br>
            <span class="font-mono text-slate-400"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
          </td>
          <td class="px-5 py-3 text-sm">
            <?php if ($log['usuario_nombre']): ?>
            <span class="font-semibold text-slate-800"><?= htmlspecialchars($log['usuario_nombre']) ?></span>
            <?php else: ?>
            <span class="text-slate-400 italic">Anónimo</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3">
            <span class="badge <?= $accionColors[$log['accion']] ?? 'badge-gray' ?>">
              <?= ucfirst(str_replace('_', ' ', $log['accion'])) ?>
            </span>
          </td>
          <td class="px-5 py-3 text-xs text-slate-500">
            <?php if ($log['tabla']): ?>
            <span class="font-semibold"><?= htmlspecialchars($log['tabla']) ?></span>
            <?php if ($log['registro_id']): ?>
            <span class="text-slate-300"> #<?= $log['registro_id'] ?></span>
            <?php endif; ?>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td class="px-5 py-3">
            <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
              <?= htmlspecialchars($log['ip'] ?? '—') ?>
            </span>
          </td>
          <td class="px-5 py-3 text-xs text-slate-500 max-w-xs truncate">
            <?= htmlspecialchars($log['descripcion'] ?? '—') ?>
          </td>
          <td class="px-5 py-3">
            <?php if ($log['datos_antes'] || $log['datos_despues']): ?>
            <a href="<?= APP_URL ?>/admin/auditoria/<?= $log['id'] ?>"
               class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-800 text-xs font-semibold">
              <i class="fas fa-eye"></i>Ver
            </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Paginación -->
  <?php if ($resultado['paginas'] > 1): ?>
  <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
    <p class="text-xs text-slate-400">
      Mostrando <?= count($resultado['datos']) ?> de <?= $resultado['total'] ?> registros
    </p>
    <div class="flex gap-1">
      <?php for ($p = 1; $p <= $resultado['paginas']; $p++): ?>
      <?php
      $params = $_GET;
      $params['pagina'] = $p;
      $qs = http_build_query($params);
      ?>
      <a href="<?= APP_URL ?>/admin/auditoria?<?= $qs ?>"
         class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
         <?= $p === $resultado['pagina'] ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
        <?= $p ?>
      </a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>