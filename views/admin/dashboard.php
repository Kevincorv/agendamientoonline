<?php
/**
 * @var array $stats
 * @var array $recientes
 * @var int $pacientesUnicos
 * @var float $tasaCancelaciones
 * @var array $especialidadesTop
 * @var array $medicosTop
 * @var array $citasPorDia
 * @var string $csrfToken
 */
require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<!-- ─── KPI Cards ─── -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6" id="kpiGrid">
  <?php
  $kpis = [
    ['kpi-total',      'Total Citas',     $stats['total']       ?? 0, 'fa-calendar-day',   '#0284c7', '#e0f2fe'],
    ['kpi-pendientes', 'Pendientes',      $stats['pendientes']  ?? 0, 'fa-clock',          '#d97706', '#fef9c3'],
    ['kpi-pacientes',  'Pacientes',       $pacientesUnicos            , 'fa-users',          '#10b981', '#d1fae5'],
    ['kpi-canceladas', 'Canc. ' . $tasaCancelaciones . '%', $stats['canceladas'] ?? 0, 'fa-times-circle', '#ef4444', '#fee2e2'],
  ];
  foreach ($kpis as [$id, $label, $val, $icon, $color, $bg]): ?>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center gap-3 fade-up" id="<?= $id ?>">
    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background:<?= $bg ?>">
      <i class="fas <?= $icon ?> text-base" style="color:<?= $color ?>"></i>
    </div>
    <div>
      <p class="text-xl font-extrabold text-slate-800 kpi-value"><?= $val ?></p>
      <p class="text-xs font-medium text-slate-400"><?= $label ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ─── Charts Row ─── -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="md:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Citas por día</h3>
      <span class="text-[10px] text-slate-400">Últimos 7 días</span>
    </div>
    <div class="chart-wrap" style="height:160px">
      <canvas id="citasPorDiaChart"></canvas>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up" style="animation-delay:0.1s">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Distribución</h3>
      <span class="text-[10px] text-slate-400">Estados</span>
    </div>
    <div class="chart-wrap" style="height:160px">
      <canvas id="citasPorEspecialidadChart"></canvas>
    </div>
  </div>
</div>

<!-- ─── Especialidades Top + Médicos Top + Próximas Citas ─── -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <!-- Especialidades top -->
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up">
    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
      <i class="fas fa-star text-amber-400 mr-1"></i> Especialidades
    </h3>
    <?php if (empty($especialidadesTop)): ?>
      <p class="text-xs text-slate-400 text-center py-4">Sin datos</p>
    <?php else: ?>
    <div class="space-y-2">
      <?php foreach ($especialidadesTop as $i => $e): ?>
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-slate-400 w-4"><?= $i + 1 ?>.</span>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold text-slate-700 truncate"><?= htmlspecialchars($e['nombre']) ?></p>
          <div class="w-full h-1.5 rounded-full bg-slate-100 mt-1">
            <div class="h-1.5 rounded-full" style="width:<?= min(100, $e['total'] * 10) ?>%;background:linear-gradient(90deg,#0284c7,#0e7490)"></div>
          </div>
        </div>
        <span class="text-xs font-bold text-slate-500"><?= $e['total'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Médicos más consultas -->
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 fade-up" style="animation-delay:0.05s">
    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
      <i class="fas fa-user-md text-sky-500 mr-1"></i> Médicos Top
    </h3>
    <?php if (empty($medicosTop)): ?>
      <p class="text-xs text-slate-400 text-center py-4">Sin datos</p>
    <?php else: ?>
    <div class="space-y-2">
      <?php foreach ($medicosTop as $i => $m): ?>
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0"
             style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
          <?= strtoupper(substr($m['nombre'], 0, 1)) ?>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold text-slate-700 truncate">Dr. <?= htmlspecialchars($m['apellido']) ?></p>
          <p class="text-[10px] text-slate-400"><?= htmlspecialchars($m['especialidad'] ?? '') ?></p>
        </div>
        <span class="text-xs font-bold text-slate-500"><?= $m['total'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Quick Actions -->
  <div class="fade-up" style="animation-delay:0.1s">
    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Acciones</h3>
    <div class="flex flex-col gap-2">
      <a href="<?= APP_URL ?>/admin/citas" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all text-sm font-semibold text-slate-700">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#0284c7,#0e7490)">
          <i class="fas fa-calendar-plus text-white text-xs"></i>
        </div>
        Nueva Cita
      </a>
      <a href="<?= APP_URL ?>/admin/medicos" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all text-sm font-semibold text-slate-700">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
          <i class="fas fa-user-md text-white text-xs"></i>
        </div>
        Nuevo Médico
      </a>
      <a href="<?= APP_URL ?>/admin/usuarios" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all text-sm font-semibold text-slate-700">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#059669,#34d399)">
          <i class="fas fa-users text-white text-xs"></i>
        </div>
        Usuarios
      </a>
      <a href="<?= APP_URL ?>/admin/horarios" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-white border border-slate-100 shadow-sm hover:shadow-md transition-all text-sm font-semibold text-slate-700">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#d97706,#f59e0b)">
          <i class="fas fa-clock text-white text-xs"></i>
        </div>
        Horarios
      </a>
    </div>
  </div>

  <!-- Próximas Citas -->
  <div class="md:col-span-3 bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden fade-up" style="animation-delay:0.05s">
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
      <div class="flex items-center gap-2.5">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#e0f2fe">
          <i class="fas fa-calendar-alt text-xs" style="color:#0284c7"></i>
        </div>
        <div>
          <h2 class="text-sm font-bold text-slate-800">Próximas Citas</h2>
          <p class="text-[10px] text-slate-400">Citas agendadas para los próximos días</p>
        </div>
      </div>
      <a href="<?= APP_URL ?>/admin/citas" class="text-xs font-bold text-sky-600 hover:text-sky-700 transition whitespace-nowrap">
        Ver todas <i class="fas fa-arrow-right ml-1"></i>
      </a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full data-table resp-table">
        <thead>
          <tr>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Especialidad</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recientes)): ?>
          <tr>
            <td colspan="6" class="text-center py-10">
              <div class="text-slate-300 text-3xl mb-2"><i class="fas fa-calendar-times"></i></div>
              <p class="text-sm font-semibold text-slate-500">Sin citas próximas</p>
              <p class="text-xs text-slate-400 mt-0.5">No hay citas agendadas para los próximos días</p>
            </td>
          </tr>
          <?php else: foreach ($recientes as $c): ?>
          <tr>
            <td data-label="Paciente">
              <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0" style="background:linear-gradient(135deg,#0284c7,#0e7490)">
                  <?= strtoupper(substr($c['nombre_paciente'], 0, 1)) ?>
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-slate-800 text-sm leading-tight truncate"><?= htmlspecialchars($c['nombre_paciente']) ?></p>
                  <p class="text-slate-400 text-[11px]"><?= htmlspecialchars($c['telefono']) ?></p>
                </div>
              </div>
            </td>
            <td data-label="Médico">
              <p class="text-sm text-slate-700 truncate">Dr. <?= htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) ?></p>
            </td>
            <td data-label="Especialidad">
              <span class="text-sm text-slate-500"><?= htmlspecialchars($c['especialidad']) ?></span>
            </td>
            <td data-label="Fecha">
              <span class="text-sm font-medium text-slate-600 whitespace-nowrap"><?= formatearFecha($c['fecha']) ?></span>
            </td>
            <td data-label="Hora">
              <span class="font-mono text-sm text-slate-700"><?= htmlspecialchars(substr($c['hora'] ?? '', 0, 5)) ?></span>
            </td>
            <td data-label="Estado">
              <?php $type = $c['estado']; $text = ucfirst($c['estado']); ?>
              <?php require __DIR__ . '/../components/badge.php'; ?>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Chart === 'undefined') return;

  var ctx1 = document.getElementById('citasPorDiaChart');
  if (ctx1) {
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        datasets: [{
          label: 'Citas',
          data: [<?= implode(',', $citasPorDia) ?>],
          backgroundColor: 'rgba(2,132,199,.55)',
          borderColor: '#0284c7',
          borderWidth: 1,
          borderRadius: 3,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 9 } } },
          x: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
      }
    });
  }

  var ctx2 = document.getElementById('citasPorEspecialidadChart');
  if (ctx2) {
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: ['Pendientes', 'Confirmadas', 'Canceladas'],
        datasets: [{
          data: [<?= $stats['pendientes'] ?? 0 ?>, <?= $stats['confirmadas'] ?? 0 ?>, <?= $stats['canceladas'] ?? 0 ?>],
          backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: { padding: 8, usePointStyle: true, font: { size: 10 } }
          }
        }
      }
    });
  }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
