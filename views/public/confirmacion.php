<?php require_once __DIR__ . '/../layouts/header_public.php'; ?>

<div class="max-w-2xl mx-auto px-4 py-16 fade-up">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-10 text-center">

    <!-- Check animado -->
    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg"
         style="background:linear-gradient(135deg,#22c55e,#16a34a)">
      <i class="fas fa-check text-white text-3xl"></i>
    </div>

    <h1 class="text-3xl font-extrabold text-slate-800 mb-2">¡Cita Registrada!</h1>
    <p class="text-slate-500 mb-8">Su solicitud fue recibida. El personal confirmará el turno pronto.</p>

    <!-- Detalle -->
    <div class="bg-slate-50 rounded-2xl p-6 text-left space-y-3 mb-7 border border-slate-100">
      <?php
      $detalles = [
        ['fa-user',        'Paciente',    $cita['nombre_paciente']],
        ['fa-user-md',     'Médico',      'Dr. ' . $cita['medico_nombre'] . ' ' . $cita['medico_apellido']],
        ['fa-stethoscope', 'Especialidad',$cita['especialidad']],
        ['fa-calendar',    'Fecha',       formatearFecha($cita['fecha'])],
        ['fa-clock',       'Hora',        substr($cita['hora'] ?? '', 0, 5)],
      ];
      foreach ($detalles as [$icon, $label, $val]): ?>
      <div class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
        <span class="flex items-center gap-2 text-slate-500 text-sm">
          <i class="fas <?= $icon ?> text-sky-400 w-4 text-center"></i> <?= $label ?>
        </span>
        <span class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($val) ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Estado -->
    <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold px-4 py-2 rounded-full mb-7">
      <i class="fas fa-clock"></i> Pendiente de Confirmación
    </div>

    <!-- Cancelar -->
    <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-7 text-left">
      <p class="text-red-700 text-sm font-semibold mb-2">
        <i class="fas fa-info-circle mr-1"></i>Guardá este enlace para cancelar si lo necesitás
      </p>
      <a href="<?= APP_URL ?>/cancelar-cita?token=<?= htmlspecialchars($cita['token_cancelacion']) ?>"
         class="text-red-500 text-xs break-all hover:underline">
        <?= APP_URL ?>/cancelar-cita?token=<?= htmlspecialchars($cita['token_cancelacion']) ?>
      </a>
    </div>

    <a href="<?= APP_URL ?>/"
       class="inline-flex items-center gap-2 text-white font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition-all"
       style="background:linear-gradient(135deg,#0284c7,#0e7490)">
      <i class="fas fa-home"></i> Volver al Inicio
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
