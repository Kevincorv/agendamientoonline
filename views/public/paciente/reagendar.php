<?php
/**
 * @var array $cita Cita actual a reagendar
 * @var string $csrfToken
 */
require_once __DIR__ . '/../../layouts/header_public.php';
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 fade-up">
  <div class="flex items-center gap-2 text-sm mb-6">
    <a href="<?= APP_URL ?>/paciente/dashboard" class="text-slate-400 hover:text-sky-600 transition">Inicio</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-700 font-semibold">Reagendar Cita</span>
  </div>

  <div class="card">
    <div class="px-5 py-4 border-b border-slate-100">
      <h1 class="text-lg font-extrabold text-slate-700 flex items-center gap-2">
        <i class="fas fa-exchange-alt text-amber-500"></i>Reagendar Cita
      </h1>
      <p class="text-xs text-slate-400 mt-1.5">
        Cita actual: <span class="font-semibold text-slate-600"><?= date('d/m/Y', strtotime($cita['fecha'])) ?> a las <?= substr($cita['hora'] ?? '', 0, 5) ?></span> &middot;
        Dr. <?= htmlspecialchars($cita['medico_nombre'] . ' ' . $cita['medico_apellido']) ?>
      </p>
    </div>

    <div class="p-5">
      <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <input type="hidden" name="id" value="<?= $cita['id'] ?>">

        <div class="floating-label">
          <select name="medico_id" onchange="this.form.submit()">
            <option value="">Seleccionar médico</option>
            <?php foreach ($medicos as $m): ?>
            <option value="<?= $m['id'] ?>" <?= (!empty($_GET['medico_id']) && (int)$_GET['medico_id'] === (int)$m['id']) ? 'selected' : '' ?>>
              Dr. <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <label>Médico</label>
        </div>

        <div class="floating-label">
          <input type="date" name="fecha" value="<?= sanitize($_GET['fecha'] ?? '') ?>"
                 min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
          <label>Fecha</label>
        </div>

        <button type="submit" class="btn btn-primary btn-sm self-end h-[42px]">
          <i class="fas fa-search"></i> Ver Horarios
        </button>
      </form>

      <?php if (!empty($_GET['medico_id']) && !empty($_GET['fecha'])): ?>
      <form method="POST" action="<?= APP_URL ?>/paciente/reagendar" data-loading>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">
        <input type="hidden" name="medico_id" value="<?= (int)($_GET['medico_id'] ?? 0) ?>">
        <input type="hidden" name="fecha" value="<?= sanitize($_GET['fecha'] ?? '') ?>">

        <div class="border-t border-slate-100 pt-4 mt-2">
          <p class="text-xs font-bold text-slate-600 mb-3 flex items-center gap-1.5">
            <i class="far fa-clock text-sky-500"></i> Horarios disponibles para <span class="text-sky-600"><?= date('d/m/Y', strtotime($_GET['fecha'])) ?></span>
          </p>

          <?php if (empty($slots)): ?>
            <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-lg">
              <i class="far fa-frown text-2xl mb-2 block"></i>
              <p class="text-sm">No hay horarios disponibles para esta fecha.</p>
            </div>
          <?php else:
            $hayDisponibles = false; ?>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
            <?php foreach ($slots as $s):
              if ($s['disponible'] ?? false):
                $hayDisponibles = true; ?>
              <label class="flex items-center justify-center px-3 py-2.5 rounded-lg border border-slate-200 cursor-pointer hover:border-sky-300 transition has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50 has-[:checked]:shadow-sm text-center">
                <input type="radio" name="hora" value="<?= $s['hora'] ?>" class="sr-only accent-sky-600">
                <span class="text-sm font-semibold text-slate-700"><?= $s['hora'] ?></span>
              </label>
            <?php endif; endforeach; ?>
            </div>

            <?php if (!$hayDisponibles): ?>
              <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-lg">
                <i class="far fa-clock text-2xl mb-2 block"></i>
                <p class="text-sm">No hay turnos disponibles para esta fecha.</p>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <?php if (!empty($slots) && $hayDisponibles): ?>
        <div class="mt-5 flex gap-3">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-check"></i> Confirmar Reagendación
          </button>
          <a href="<?= APP_URL ?>/paciente/dashboard" class="btn btn-secondary">Cancelar</a>
        </div>
        <?php endif; ?>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
