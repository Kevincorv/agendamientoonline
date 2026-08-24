<?php require_once __DIR__ . '/../../layouts/header_public.php'; ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 fade-up">
  <!-- Breadcrumb -->
  <div class="flex items-center gap-2 text-sm mb-6">
    <a href="<?= APP_URL ?>/paciente/dashboard" class="text-slate-400 hover:text-sky-600 transition">Inicio</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-700 font-semibold">Historial de Citas</span>
  </div>

  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h1 class="text-lg font-extrabold text-slate-700">
        <i class="fas fa-history mr-2 text-slate-400"></i>Historial de Citas
      </h1>
    </div>

    <?php if (empty($citas)): ?>
      <div class="text-center py-12 text-slate-400">
        <i class="far fa-calendar-alt text-4xl mb-3 block mx-auto"></i>
        <p class="text-sm font-medium">No hay citas registradas</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full data-table text-sm">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Especialidad</th>
              <th>Médico</th>
              <th>Estado</th>
              <th class="text-right w-16"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <?php foreach ($citas as $c): ?>
            <tr class="hover:bg-slate-50 transition">
              <td data-label="Fecha">
                <span class="text-sm font-medium text-slate-700"><?= date('d/m/Y', strtotime($c['fecha'])) ?></span>
              </td>
              <td data-label="Hora">
                <span class="font-mono text-sm text-slate-600"><?= substr($c['hora'] ?? '', 0, 5) ?></span>
              </td>
              <td data-label="Especialidad">
                <span class="text-sm text-slate-700"><?= htmlspecialchars($c['especialidad']) ?></span>
              </td>
              <td data-label="Médico">
                <span class="text-sm text-slate-600">Dr. <?= htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) ?></span>
              </td>
              <td data-label="Estado">
                <span class="badge badge-<?= $c['estado'] === 'atendida' ? 'success' : ($c['estado'] === 'cancelada' ? 'danger' : ($c['estado'] === 'no asistio' ? 'secondary' : 'warning')) ?>">
                  <?= ucfirst($c['estado']) ?>
                </span>
              </td>
              <td data-label="" class="text-right">
                <a href="<?= APP_URL ?>/paciente/comprobante?id=<?= $c['id'] ?>" target="_blank"
                   class="btn btn-icon btn-xs btn-ghost text-sky-600" title="Comprobante">
                  <i class="fas fa-print"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
