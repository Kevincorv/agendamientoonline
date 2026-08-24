<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="max-w-3xl fade-up">
  <div class="card p-7">
    <div class="flex items-center gap-3 mb-7">
      <a href="<?= APP_URL ?>/admin/auditoria"
         class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
        <i class="fas fa-arrow-left text-xs"></i>
      </a>
      <h2 class="font-bold text-slate-800 text-lg">Detalle del Evento #<?= $log['id'] ?></h2>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-7 text-sm">
      <?php
      $detalles = [
        ['Fecha y Hora', date('d/m/Y H:i:s', strtotime($log['created_at']))],
        ['Usuario',      $log['usuario_nombre'] ?? 'Anónimo'],
        ['Acción',       ucfirst(str_replace('_',' ',$log['accion']))],
        ['Tabla',        $log['tabla'] ?? '—'],
        ['Registro ID',  $log['registro_id'] ?? '—'],
        ['IP',           $log['ip'] ?? '—'],
      ];
      foreach ($detalles as [$label, $val]): ?>
      <div class="bg-slate-50 rounded-xl p-4">
        <p class="text-slate-400 text-xs mb-1"><?= $label ?></p>
        <p class="font-semibold text-slate-800"><?= htmlspecialchars((string)$val) ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($log['descripcion']): ?>
    <div class="bg-slate-50 rounded-xl p-4 mb-5">
      <p class="text-slate-400 text-xs mb-1">Descripción</p>
      <p class="text-slate-700 text-sm"><?= htmlspecialchars($log['descripcion']) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($log['user_agent']): ?>
    <div class="bg-slate-50 rounded-xl p-4 mb-5">
      <p class="text-slate-400 text-xs mb-1">User Agent</p>
      <p class="text-slate-600 text-xs break-all"><?= htmlspecialchars($log['user_agent']) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($log['datos_antes'] || $log['datos_despues']): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <?php if ($log['datos_antes']): ?>
      <div>
        <p class="text-xs font-semibold text-red-600 mb-2"><i class="fas fa-minus-circle mr-1"></i>Datos Antes</p>
        <pre class="bg-red-50 border border-red-100 rounded-xl p-4 text-xs text-red-800 overflow-x-auto"><?= htmlspecialchars(json_encode($log['datos_antes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
      </div>
      <?php endif; ?>
      <?php if ($log['datos_despues']): ?>
      <div>
        <p class="text-xs font-semibold text-emerald-600 mb-2"><i class="fas fa-plus-circle mr-1"></i>Datos Después</p>
        <pre class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-xs text-emerald-800 overflow-x-auto"><?= htmlspecialchars(json_encode($log['datos_despues'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>