<?php require_once __DIR__ . '/../../layouts/header_public.php'; ?>

<div class="max-w-lg mx-auto px-4 sm:px-6 py-6 fade-up">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2 text-sm">
      <a href="<?= APP_URL ?>/paciente/dashboard" class="text-slate-400 hover:text-sky-600 transition">Inicio</a>
      <span class="text-slate-300">/</span>
      <span class="text-slate-700 font-semibold">Comprobante</span>
    </div>
    <button onclick="window.print()" class="btn btn-secondary btn-sm">
      <i class="fas fa-print"></i> Imprimir
    </button>
  </div>

  <div class="card p-6" id="comprobante">
    <!-- Header -->
    <div class="text-center border-b border-slate-100 pb-4 mb-4">
      <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white mx-auto mb-3"
           style="background:linear-gradient(135deg,#0284c7,#0e7490)">
        <i class="fas fa-heartbeat text-xl"></i>
      </div>
      <h2 class="text-lg font-extrabold text-slate-800"><?= htmlspecialchars(APP_NAME) ?></h2>
      <p class="text-xs text-slate-400">Comprobante de Cita Médica</p>
    </div>

    <!-- Datos -->
    <div class="space-y-3 text-sm">
      <div class="flex justify-between py-2 border-b border-slate-50">
        <span class="text-slate-500">Paciente</span>
        <span class="font-semibold text-slate-800"><?= htmlspecialchars($cita['nombre_paciente']) ?></span>
      </div>
      <div class="flex justify-between py-2 border-b border-slate-50">
        <span class="text-slate-500">Especialidad</span>
        <span class="font-semibold text-slate-800"><?= htmlspecialchars($cita['especialidad']) ?></span>
      </div>
      <div class="flex justify-between py-2 border-b border-slate-50">
        <span class="text-slate-500">Médico</span>
        <span class="font-semibold text-slate-800">Dr. <?= htmlspecialchars($cita['medico_nombre'] . ' ' . $cita['medico_apellido']) ?></span>
      </div>
      <div class="flex justify-between py-2 border-b border-slate-50">
        <span class="text-slate-500">Fecha</span>
        <span class="font-semibold text-slate-800"><?= date('d/m/Y', strtotime($cita['fecha'])) ?></span>
      </div>
      <div class="flex justify-between py-2 border-b border-slate-50">
        <span class="text-slate-500">Hora</span>
        <span class="font-semibold text-slate-800"><?= substr($cita['hora'] ?? '', 0, 5) ?></span>
      </div>
      <div class="flex justify-between py-2">
        <span class="text-slate-500">Estado</span>
        <span class="badge badge-<?= $cita['estado'] === 'pendiente' ? 'warning' : ($cita['estado'] === 'confirmada' ? 'info' : ($cita['estado'] === 'cancelada' ? 'danger' : 'success')) ?>">
          <?= ucfirst($cita['estado']) ?>
        </span>
      </div>
      <?php if (!empty($cita['motivo'])): ?>
      <div class="py-2 border-t border-slate-100">
        <span class="text-slate-500 text-xs block mb-1">Motivo de consulta</span>
        <p class="text-slate-700"><?= nl2br(htmlspecialchars($cita['motivo'])) ?></p>
      </div>
      <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="text-center text-[10px] text-slate-400 mt-6 pt-4 border-t border-slate-100">
      <p>Generado el <?= date('d/m/Y H:i') ?></p>
      <p class="mt-1">Presentá este comprobante el día de tu consulta</p>
    </div>
  </div>
</div>

<style>
@media print {
  header, footer, .btn, .no-print { display: none !important; }
  #comprobante { box-shadow: none !important; border: 1px solid #e2e8f0; }
  body { background: white !important; }
}
</style>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
