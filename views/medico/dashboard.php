<?php
/**
 * @var array $medico
 * @var string $fecha
 * @var array $citas
 * @var int $totalAtendidos
 * @var int $totalHoy
 * @var int $pendientesHoy
 * @var array|null $proximaCita
 * @var string $csrfToken
 */
require_once __DIR__ . '/../layouts/header_admin.php';
$dias = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$diaNombre = $dias[(int)date('w', strtotime($fecha))];
$esHoy = $fecha === date('Y-m-d');
?>

<div class="fade-up max-w-6xl mx-auto">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-800">
        <i class="fas fa-stethoscope text-sky-500 mr-2"></i>Mi Dashboard
      </h1>
      <p class="text-sm text-slate-500 mt-0.5">
        Dr. <?= htmlspecialchars($medico['nombre'] . ' ' . $medico['apellido']) ?>
        — <?= htmlspecialchars($medico['especialidad'] ?? '') ?>
      </p>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?= APP_URL ?>/medico/disponibilidad" class="btn btn-sm <?= $medico['disponible'] ? 'btn-success' : 'btn-secondary' ?>">
        <i class="fas <?= $medico['disponible'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        <?= $medico['disponible'] ? 'Disponible' : 'No disponible' ?>
      </a>
      <a href="<?= APP_URL ?>/medico/perfil" class="btn btn-secondary btn-sm">
        <i class="fas fa-user-edit"></i>
      </a>
      <a href="<?= APP_URL ?>/admin/logout" class="btn btn-ghost btn-sm text-red-500">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </div>

  <!-- KPIs -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card">
      <div class="kpi-icon" style="background:#dbeafe;color:#2563eb">
        <i class="fas fa-calendar-day"></i>
      </div>
      <p class="kpi-value"><?= $totalHoy ?></p>
      <p class="kpi-label">Citas hoy</p>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon" style="background:#fef3c7;color:#d97706">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <p class="kpi-value"><?= $pendientesHoy ?></p>
      <p class="kpi-label">Pendientes</p>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon" style="background:#d1fae5;color:#059669">
        <i class="fas fa-check-double"></i>
      </div>
      <p class="kpi-value"><?= $totalAtendidos ?></p>
      <p class="kpi-label">Atendidos (total)</p>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon" style="background:#e0e7ff;color:#4f46e5">
        <i class="fas fa-clock"></i>
      </div>
      <p class="kpi-value"><?= $proximaCita ? date('H:i', strtotime($proximaCita['hora'])) : '--' ?></p>
      <p class="kpi-label">Próxima cita</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Agenda del día -->
    <div class="lg:col-span-2 card">
      <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-700">
          <i class="fas fa-list mr-2 text-sky-500"></i>
          Agenda — <?= $diaNombre ?> <?= date('d/m/Y', strtotime($fecha)) ?>
          <?php if ($esHoy): ?>
          <span class="badge badge-info text-[10px] ml-2">Hoy</span>
          <?php endif; ?>
        </h2>
        <form method="GET" class="flex items-center gap-2">
          <input type="date" name="fecha" value="<?= $fecha ?>"
                 class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-sky-400"
                 onchange="this.form.submit()">
        </form>
      </div>

      <?php if (empty($citas)): ?>
        <div class="text-center py-10 text-slate-400">
          <i class="far fa-calendar-check text-4xl mb-3 block"></i>
          <p class="text-sm font-medium">No hay citas para esta fecha</p>
        </div>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr class="text-slate-500 text-xs uppercase">
              <th class="px-4 py-3 text-left font-semibold">Hora</th>
              <th class="px-4 py-3 text-left font-semibold">Paciente</th>
              <th class="px-4 py-3 text-left font-semibold">Contacto</th>
              <th class="px-4 py-3 text-left font-semibold">Motivo</th>
              <th class="px-4 py-3 text-left font-semibold">Estado</th>
              <th class="px-4 py-3 text-left font-semibold">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <?php foreach ($citas as $c): ?>
            <tr class="hover:bg-slate-50 transition <?= ($proximaCita && $c['id'] === $proximaCita['id']) ? 'bg-sky-50/50' : '' ?>">
              <td class="px-4 py-3 font-bold text-sky-600"><?= substr($c['hora'] ?? '', 0, 5) ?></td>
              <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($c['nombre_paciente']) ?></td>
              <td class="px-4 py-3 text-xs text-slate-500">
                <?= htmlspecialchars($c['telefono'] ?? '') ?>
                <?php if ($c['email']): ?><br><?= htmlspecialchars($c['email']) ?><?php endif; ?>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 max-w-[120px] truncate">
                <?= htmlspecialchars($c['motivo'] ?? '') ?>
              </td>
              <td class="px-4 py-3">
                <span class="badge badge-<?= $c['estado'] === 'pendiente' ? 'warning' : ($c['estado'] === 'confirmada' ? 'info' : ($c['estado'] === 'atendida' ? 'success' : ($c['estado'] === 'cancelada' ? 'danger' : 'secondary'))) ?>">
                  <?= ucfirst($c['estado']) ?>
                </span>
              </td>
              <td class="px-4 py-3">
                <?php if ($c['estado_id'] == 1 || $c['estado_id'] == 2): ?>
                <form method="POST" action="<?= APP_URL ?>/admin/citas/<?= $c['id'] ?>/estado" class="flex gap-1">
                  <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                  <input type="hidden" name="cita_id" value="<?= $c['id'] ?>">
                  <select name="estado_id" onchange="this.form.submit()"
                          class="text-xs border border-slate-200 rounded px-2 py-1.5 focus:outline-none cursor-pointer">
                    <option value="4">Atendida</option>
                    <option value="5">No asistió</option>
                  </select>
                </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="space-y-5">
      <!-- Próxima cita -->
      <div class="card">
        <div class="px-5 py-3 border-b border-slate-100">
          <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Próxima Cita</h3>
        </div>
        <div class="p-5">
          <?php if ($proximaCita): ?>
          <div class="text-center">
            <div class="text-3xl font-extrabold text-sky-600"><?= date('H:i', strtotime($proximaCita['hora'])) ?></div>
            <p class="text-sm font-semibold text-slate-700 mt-2"><?= htmlspecialchars($proximaCita['nombre_paciente']) ?></p>
            <p class="text-xs text-slate-400 mt-1"><?= htmlspecialchars($proximaCita['motivo'] ?? 'Sin motivo') ?></p>
          </div>
          <?php else: ?>
          <div class="text-center text-slate-400 py-4">
            <i class="far fa-check-circle text-2xl mb-2 block"></i>
            <p class="text-xs">No hay más citas para hoy</p>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Calendario mini -->
      <div class="card">
        <div class="px-5 py-3 border-b border-slate-100">
          <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
            <i class="far fa-calendar-alt mr-1"></i> Calendario
          </h3>
        </div>
        <div class="p-3">
          <?php
            $mes = (int)date('m', strtotime($fecha));
            $anio = (int)date('Y', strtotime($fecha));
            $primerDia = mktime(0, 0, 0, $mes, 1, $anio);
            $diasEnMes = (int)date('t', $primerDia);
            $inicioSemana = (int)date('w', $primerDia);
            $hoy = (int)date('d');
            $mesActual = date('m-Y');
            $diaSeleccionado = (int)date('d', strtotime($fecha));
          ?>
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-700"><?= ucfirst(strftime('%B %Y', $primerDia)) ?></span>
          </div>
          <div class="grid grid-cols-7 gap-0 text-center">
            <?php foreach (['Do','Lu','Ma','Mi','Ju','Vi','Sá'] as $d): ?>
            <span class="text-[10px] text-slate-400 font-semibold py-1"><?= $d ?></span>
            <?php endforeach; ?>
            <?php for ($i = 0; $i < $inicioSemana; $i++): ?>
            <span></span>
            <?php endfor; ?>
            <?php for ($d = 1; $d <= $diasEnMes; $d++): ?>
              <?php
                $fechaIter = sprintf('%04d-%02d-%02d', $anio, $mes, $d);
                $esSeleccionado = $d === $diaSeleccionado;
                $esHoyCal = ($d === $hoy && $mesActual === date('m-Y'));
              ?>
              <a href="<?= APP_URL ?>/medico/dashboard?fecha=<?= $fechaIter ?>"
                 class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs transition
                   <?= $esSeleccionado ? 'bg-sky-500 text-white font-bold' : ($esHoyCal ? 'bg-sky-100 text-sky-700 font-bold' : 'text-slate-600 hover:bg-slate-100') ?>">
                <?= $d ?>
              </a>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
