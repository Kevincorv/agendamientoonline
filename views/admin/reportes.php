<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Reportes</h1>
    <p class="text-sm text-slate-400">Estadísticas y reportes del sistema</p>
  </div>
  <a href="<?= APP_URL ?>/admin/reportes/exportar?<?= http_build_query($_GET) ?>" class="btn btn-secondary btn-sm">
    <i class="fas fa-file-csv"></i> Exportar CSV
  </a>
</div>

<!-- Filtros -->
<form method="GET" action="<?= APP_URL ?>/admin/reportes"
      class="card p-4 sm:p-5 mb-5 fade-up">
  <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
  <div class="flex flex-wrap gap-3 sm:gap-4 items-end">
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Desde</label>
      <input type="date" name="desde" value="<?= htmlspecialchars($filtros['desde']) ?>" class="input-field">
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Hasta</label>
      <input type="date" name="hasta" value="<?= htmlspecialchars($filtros['hasta']) ?>" class="input-field">
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Médico</label>
      <select name="medico_id" class="input-field">
        <option value="">Todos</option>
        <?php foreach ($medicos as $m): ?>
        <option value="<?= $m['id'] ?>" <?= ($filtros['medico_id'] ?? 0) == $m['id'] ? 'selected' : '' ?>>
          Dr. <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Especialidad</label>
      <select name="especialidad_id" class="input-field">
        <option value="">Todas</option>
        <?php foreach ($especialidades as $e): ?>
        <option value="<?= $e['id'] ?>" <?= ($filtros['especialidad_id'] ?? 0) == $e['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($e['nombre']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-500 mb-1.5">Estado</label>
      <select name="estado_id" class="input-field">
        <option value="">Todos</option>
        <option value="1" <?= ($filtros['estado_id'] ?? 0) == 1 ? 'selected' : '' ?>>Pendiente</option>
        <option value="2" <?= ($filtros['estado_id'] ?? 0) == 2 ? 'selected' : '' ?>>Confirmada</option>
        <option value="3" <?= ($filtros['estado_id'] ?? 0) == 3 ? 'selected' : '' ?>>Cancelada</option>
        <option value="4" <?= ($filtros['estado_id'] ?? 0) == 4 ? 'selected' : '' ?>>Atendida</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter"></i> Filtrar
      </button>
      <a href="<?= APP_URL ?>/admin/reportes" class="btn btn-secondary">
        <i class="fas fa-times"></i>
      </a>
    </div>
  </div>
</form>

<!-- KPI Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 text-center fade-up">
    <p class="text-2xl font-extrabold text-slate-800"><?= $stats['total'] ?></p>
    <p class="text-xs font-medium text-slate-400">Total Citas</p>
  </div>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 text-center fade-up">
    <p class="text-2xl font-extrabold text-amber-500"><?= $stats['pendientes'] ?></p>
    <p class="text-xs font-medium text-slate-400">Pendientes</p>
  </div>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 text-center fade-up">
    <p class="text-2xl font-extrabold text-emerald-500"><?= $stats['confirmadas'] ?></p>
    <p class="text-xs font-medium text-slate-400">Confirmadas</p>
  </div>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 text-center fade-up">
    <p class="text-2xl font-extrabold text-red-500"><?= $stats['canceladas'] ?></p>
    <p class="text-xs font-medium text-slate-400">Canceladas</p>
  </div>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 text-center fade-up">
    <p class="text-2xl font-extrabold text-sky-500"><?= $stats['atendidas'] ?></p>
    <p class="text-xs font-medium text-slate-400">Atendidas</p>
  </div>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 text-center fade-up">
    <p class="text-2xl font-extrabold text-purple-500"><?= $stats['pacientes'] ?></p>
    <p class="text-xs font-medium text-slate-400">Pacientes</p>
  </div>
</div>

<?php
  function urlConTab(string $nuevoTab): string {
      $qs = $_GET;
      $qs['tab'] = $nuevoTab;
      return '?' . http_build_query($qs);
  }
?>
<!-- Tab Navigation -->
<div class="flex gap-1 mb-5 border-b border-slate-200 overflow-x-auto fade-up">
  <a href="<?= urlConTab('resumen') ?>" class="px-4 py-2.5 text-sm font-semibold whitespace-nowrap border-b-2 transition <?= $tab === 'resumen' ? 'text-sky-600 border-sky-500' : 'text-slate-400 border-transparent hover:text-slate-600' ?>">
    <i class="fas fa-chart-pie mr-1.5"></i>Resumen
  </a>
  <a href="<?= urlConTab('medicos') ?>" class="px-4 py-2.5 text-sm font-semibold whitespace-nowrap border-b-2 transition <?= $tab === 'medicos' ? 'text-sky-600 border-sky-500' : 'text-slate-400 border-transparent hover:text-slate-600' ?>">
    <i class="fas fa-user-md mr-1.5"></i>Por Médico
  </a>
  <a href="<?= urlConTab('especialidades') ?>" class="px-4 py-2.5 text-sm font-semibold whitespace-nowrap border-b-2 transition <?= $tab === 'especialidades' ? 'text-sky-600 border-sky-500' : 'text-slate-400 border-transparent hover:text-slate-600' ?>">
    <i class="fas fa-stethoscope mr-1.5"></i>Por Especialidad
  </a>
  <a href="<?= urlConTab('detalle') ?>" class="px-4 py-2.5 text-sm font-semibold whitespace-nowrap border-b-2 transition <?= $tab === 'detalle' ? 'text-sky-600 border-sky-500' : 'text-slate-400 border-transparent hover:text-slate-600' ?>">
    <i class="fas fa-list mr-1.5"></i>Detalle
  </a>
</div>

<?php if ($tab === 'medicos'): ?>
<!-- Tabla por Médico -->
<div class="card overflow-hidden fade-up">
  <div class="px-5 py-3 border-b border-slate-100">
    <p class="text-xs font-bold text-slate-600">Citas por Médico</p>
  </div>
  <div class="overflow-x-auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Médico</th>
          <th>Especialidad</th>
          <th class="text-center">Total</th>
          <th class="text-center">Atendidas</th>
          <th class="text-center">Canceladas</th>
          <th class="text-center">Efectividad</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($porMedico)):
            $cols = 6; $message = 'Sin datos'; require __DIR__ . '/../components/table_empty.php'; ?>
        <?php else: foreach ($porMedico as $m): ?>
        <tr>
          <td class="font-semibold text-slate-700">Dr. <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?></td>
          <td class="text-slate-500"><?= htmlspecialchars($m['especialidad'] ?? '—') ?></td>
          <td class="text-center font-bold"><?= $m['total'] ?></td>
          <td class="text-center text-emerald-600 font-semibold"><?= $m['atendidas'] ?></td>
          <td class="text-center text-red-500 font-semibold"><?= $m['canceladas'] ?></td>
          <td class="text-center">
            <?php $efectividad = $m['total'] > 0 ? round(($m['atendidas'] / $m['total']) * 100) : 0; ?>
            <span class="badge badge-<?= $efectividad >= 70 ? 'success' : ($efectividad >= 40 ? 'warning' : 'danger') ?>"><?= $efectividad ?>%</span>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php elseif ($tab === 'especialidades'): ?>
<!-- Tabla por Especialidad -->
<div class="card overflow-hidden fade-up">
  <div class="px-5 py-3 border-b border-slate-100">
    <p class="text-xs font-bold text-slate-600">Citas por Especialidad</p>
  </div>
  <div class="overflow-x-auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Especialidad</th>
          <th class="text-center">Total</th>
          <th class="text-center">Atendidas</th>
          <th class="text-center">Canceladas</th>
          <th class="text-center">Efectividad</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($porEspecialidad)):
            $cols = 5; $message = 'Sin datos'; require __DIR__ . '/../components/table_empty.php'; ?>
        <?php else: foreach ($porEspecialidad as $e): ?>
        <tr>
          <td class="font-semibold text-slate-700"><?= htmlspecialchars($e['nombre']) ?></td>
          <td class="text-center font-bold"><?= $e['total'] ?></td>
          <td class="text-center text-emerald-600 font-semibold"><?= $e['atendidas'] ?></td>
          <td class="text-center text-red-500 font-semibold"><?= $e['canceladas'] ?></td>
          <td class="text-center">
            <?php $efectividad = $e['total'] > 0 ? round(($e['atendidas'] / $e['total']) * 100) : 0; ?>
            <span class="badge badge-<?= $efectividad >= 70 ? 'success' : ($efectividad >= 40 ? 'warning' : 'danger') ?>"><?= $efectividad ?>%</span>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php elseif ($tab === 'detalle'): ?>
<!-- Tabla detalle -->
<div class="card overflow-hidden fade-up">
  <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
    <p class="text-xs text-slate-400">
      <span class="font-semibold text-slate-600"><?= $citasPaginado['total'] ?></span> citas encontradas
      <span class="hidden sm:inline">· Pág. <?= $citasPaginado['pagina'] ?>/<?= $citasPaginado['paginas'] ?></span>
    </p>
  </div>
  <div class="overflow-x-auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Paciente</th>
          <th>Teléfono</th>
          <th>Médico</th>
          <th>Especialidad</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($citasPaginado['datos'])):
            $cols = 7; $message = 'Sin datos'; require __DIR__ . '/../components/table_empty.php'; ?>
        <?php else: foreach ($citasPaginado['datos'] as $c): ?>
        <tr>
          <td class="font-semibold text-slate-700"><?= htmlspecialchars($c['nombre_paciente']) ?></td>
          <td class="text-slate-500"><?= htmlspecialchars($c['telefono']) ?></td>
          <td class="text-slate-600">Dr. <?= htmlspecialchars($c['medico_nombre'] . ' ' . $c['medico_apellido']) ?></td>
          <td class="text-slate-500"><?= htmlspecialchars($c['especialidad']) ?></td>
          <td class="text-slate-700"><?= formatearFecha($c['fecha']) ?></td>
          <td class="font-mono text-slate-600"><?= htmlspecialchars(substr($c['hora'] ?? '', 0, 5)) ?></td>
          <td>
            <?php $type = $c['estado']; $text = ucfirst($c['estado']); ?>
            <?php require __DIR__ . '/../components/badge.php'; ?>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($citasPaginado['paginas'] > 1): ?>
  <div class="pagination flex-wrap">
    <p class="text-xs text-slate-400 mr-auto">Página <?= $citasPaginado['pagina'] ?> de <?= $citasPaginado['paginas'] ?></p>
    <div class="flex gap-1">
      <?php $qs = $_GET; $rango = 2; $inicio = max(1, $citasPaginado['pagina'] - $rango); $final = min($citasPaginado['paginas'], $citasPaginado['pagina'] + $rango); ?>
      <?php if ($citasPaginado['pagina'] > 1): $qs['pagina'] = 1; ?>
      <a href="<?= APP_URL ?>/admin/reportes?<?= http_build_query($qs) ?>" class="page-btn"><i class="fas fa-angle-double-left"></i></a>
      <?php endif; ?>
      <?php for ($p = $inicio; $p <= $final; $p++): $qs['pagina'] = $p; ?>
      <a href="<?= APP_URL ?>/admin/reportes?<?= http_build_query($qs) ?>" class="page-btn <?= $p === $citasPaginado['pagina'] ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <?php if ($citasPaginado['pagina'] < $citasPaginado['paginas']): $qs['pagina'] = $citasPaginado['paginas']; ?>
      <a href="<?= APP_URL ?>/admin/reportes?<?= http_build_query($qs) ?>" class="page-btn"><i class="fas fa-angle-double-right"></i></a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php else: ?>
<!-- Resumen - Gráficos -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 fade-up">
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Distribución por Estado</h3>
    <div class="chart-wrap" style="height:200px">
      <canvas id="estadoChart"></canvas>
    </div>
  </div>
  <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Top 5 Médicos</h3>
    <div class="chart-wrap" style="height:200px">
      <canvas id="medicosChart"></canvas>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Chart === 'undefined') return;

  var ctx1 = document.getElementById('estadoChart');
  if (ctx1) {
    new Chart(ctx1, {
      type: 'doughnut',
      data: {
        labels: ['Pendientes', 'Confirmadas', 'Canceladas', 'Atendidas'],
        datasets: [{
          data: [<?= $stats['pendientes'] ?>, <?= $stats['confirmadas'] ?>, <?= $stats['canceladas'] ?>, <?= $stats['atendidas'] ?>],
          backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#0284c7'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: { padding: 8, usePointStyle: true, font: { size: 10 } }
          }
        }
      }
    });
  }

  var ctx2 = document.getElementById('medicosChart');
  if (ctx2) {
    var labels = [<?php foreach (array_slice($porMedico, 0, 5) as $m): ?>'Dr. <?= htmlspecialchars($m['apellido'], ENT_QUOTES) ?>',<?php endforeach; ?>];
    var data = [<?php foreach (array_slice($porMedico, 0, 5) as $m): ?><?= $m['total'] ?>,<?php endforeach; ?>];
    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Citas',
          data: data,
          backgroundColor: 'rgba(2,132,199,.55)',
          borderColor: '#0284c7',
          borderWidth: 1,
          borderRadius: 3,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 9 } } },
          y: { grid: { display: false }, ticks: { font: { size: 9 } } }
        }
      }
    });
  }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
