<?php require_once __DIR__ . '/../layouts/header_public.php'; ?>

<div class="max-w-lg mx-auto px-4 py-16 fade-up">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-10">

    <div class="text-center mb-7">
      <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-md"
           style="background:linear-gradient(135deg,#ef4444,#dc2626)">
        <i class="fas fa-calendar-times text-white text-2xl"></i>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-800">Cancelar Cita</h1>
      <p class="text-slate-400 text-sm mt-1">Esta acción no se puede deshacer</p>
    </div>

    <!-- Resumen -->
    <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 space-y-2.5 text-sm mb-7">
      <?php
      $rows = [
        ['Paciente', $cita['nombre_paciente']],
        ['Médico',   'Dr. ' . $cita['medico_nombre'] . ' ' . $cita['medico_apellido']],
        ['Fecha',    formatearFecha($cita['fecha'])],
        ['Hora',     substr($cita['hora'] ?? '', 0, 5)],
      ];
      foreach ($rows as [$label, $val]): ?>
      <div class="flex justify-between items-center border-b border-slate-100 last:border-0 pb-2 last:pb-0">
        <span class="text-slate-400"><?= $label ?></span>
        <span class="font-semibold text-slate-800"><?= htmlspecialchars($val) ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <form method="POST" action="<?= APP_URL ?>/cancelar-cita">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input type="hidden" name="token" value="<?= htmlspecialchars($cita['token_cancelacion']) ?>">
      <button type="submit"
              class="w-full py-3.5 rounded-xl text-white font-bold text-base shadow-lg hover:opacity-90 transition mb-3"
              style="background:linear-gradient(135deg,#ef4444,#dc2626)">
        <i class="fas fa-times-circle mr-2"></i>Confirmar Cancelación
      </button>
    </form>

    <a href="<?= APP_URL ?>/"
       class="block text-center text-slate-400 hover:text-slate-600 text-sm font-medium transition py-2">
      Volver sin cancelar
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
