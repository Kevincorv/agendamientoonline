<?php require_once __DIR__ . '/../layouts/header_public.php'; ?>
<?php
$medicoSel      = isset($medicoSel)      ? $medicoSel      : null;
$especialidades = isset($especialidades) ? $especialidades : [];
$medicos        = isset($medicos)        ? $medicos        : [];
$slots          = isset($slots)          ? $slots          : [];
$csrfToken      = isset($csrfToken)      ? $csrfToken      : '';
?>

<div class="max-w-4xl mx-auto px-4 py-12">

  <div class="text-center mb-10 fade-up">
    <h1 class="text-3xl font-extrabold text-slate-800">Agendar Cita Médica</h1>
    <p class="text-slate-500 mt-2">Seguí los pasos para reservar tu turno</p>
  </div>

  <?php
  $step = 1;
  if (!empty($_GET['especialidad_id'])) $step = 2;
  if (!empty($_GET['medico_id']))       $step = 3;
  if (!empty($_GET['fecha']))           $step = 4;
  if (!empty($_GET['hora']))            $step = 5;
  $steps = ['Especialidad','Médico','Fecha','Horario','Datos'];
  ?>
  <div class="flex items-center justify-center gap-2 mb-10 fade-up">
    <?php foreach ($steps as $i => $label): $n = $i + 1; ?>
    <div class="flex items-center gap-2">
      <div class="flex flex-col items-center">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all
          <?= $n < $step ? 'text-white' : ($n === $step ? 'text-white' : 'bg-slate-100 text-slate-400') ?>"
          style="<?= $n <= $step ? 'background:linear-gradient(135deg,#0284c7,#0e7490)' : '' ?>">
          <?= $n < $step ? '<i class="fas fa-check"></i>' : $n ?>
        </div>
        <span class="text-xs mt-1 font-medium <?= $n === $step ? 'text-sky-600' : 'text-slate-400' ?>"><?= $label ?></span>
      </div>
      <?php if ($n < count($steps)): ?>
      <div class="w-8 h-0.5 mb-4 <?= $n < $step ? 'bg-sky-400' : 'bg-slate-200' ?>"></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if (!isset($_GET['especialidad_id'])): ?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
    <h2 class="font-bold text-slate-800 text-lg mb-6 flex items-center gap-2">
      <span class="step-badge">1</span> Seleccioná la Especialidad
    </h2>
    <?php if (empty($especialidades)): ?>
    <p class="text-slate-400 text-center py-8">No hay especialidades disponibles.</p>
    <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <?php foreach ($especialidades as $esp): ?>
      <a href="<?= APP_URL ?>/agendar?especialidad_id=<?= $esp['id'] ?>"
         class="border-2 rounded-xl p-5 text-center transition-all card-hover
         <?= (isset($_GET['especialidad_id']) && $_GET['especialidad_id'] == $esp['id']) ? 'border-sky-500 bg-sky-50' : 'border-slate-100 hover:border-sky-300 hover:bg-sky-50' ?>">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3"
             style="background:linear-gradient(135deg,#e0f2fe,#bae6fd)">
          <i class="fas <?= htmlspecialchars($esp['icono'] ?? 'fa-stethoscope') ?> text-sky-600 text-xl"></i>
        </div>
        <p class="font-semibold text-slate-700 text-sm"><?= htmlspecialchars($esp['nombre']) ?></p>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php elseif (!isset($_GET['medico_id'])): ?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
    <div class="flex items-center gap-3 mb-6">
      <a href="<?= APP_URL ?>/agendar" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
        <i class="fas fa-arrow-left text-xs"></i>
      </a>
      <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
        <span class="step-badge">2</span> Seleccioná el Médico
      </h2>
    </div>
    <?php if (empty($medicos)): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-center text-amber-700">
      <i class="fas fa-exclamation-triangle text-2xl mb-2 block"></i>
      No hay médicos disponibles para esta especialidad.
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php foreach ($medicos as $med): ?>
      <a href="<?= APP_URL ?>/agendar?especialidad_id=<?= (int)$_GET['especialidad_id'] ?>&medico_id=<?= $med['id'] ?>"
         class="border-2 rounded-xl p-5 flex items-center gap-4 transition-all card-hover">
        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
             style="background:linear-gradient(135deg,#0284c7,#0e7490)">
          <?= strtoupper(substr($med['nombre'], 0, 1)) ?>
        </div>
        <div>
          <p class="font-bold text-slate-800">Dr. <?= htmlspecialchars($med['nombre'] . ' ' . $med['apellido']) ?></p>
          <p class="text-sky-600 text-xs font-medium"><?= htmlspecialchars($med['especialidad'] ?? '') ?></p>
          <?php if (!empty($med['descripcion'])): ?>
          <p class="text-slate-400 text-xs mt-0.5"><?= htmlspecialchars($med['descripcion']) ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php elseif (!isset($_GET['fecha'])): ?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
    <div class="flex items-center gap-3 mb-6">
      <a href="<?= APP_URL ?>/agendar?especialidad_id=<?= htmlspecialchars($_GET['especialidad_id']) ?>"
         class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
        <i class="fas fa-arrow-left text-xs"></i>
      </a>
      <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
        <span class="step-badge">3</span> Seleccioná la Fecha
      </h2>
    </div>

    <?php if (!is_null($medicoSel)): ?>
    <div class="flex items-center gap-3 p-4 bg-sky-50 border border-sky-100 rounded-xl mb-6">
      <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0"
           style="background:linear-gradient(135deg,#0284c7,#0e7490)">
        <?= strtoupper(substr($medicoSel['nombre'], 0, 1)) ?>
      </div>
      <div>
        <p class="font-semibold text-slate-800 text-sm">Dr. <?= htmlspecialchars($medicoSel['nombre'] . ' ' . $medicoSel['apellido']) ?></p>
        <p class="text-sky-600 text-xs"><?= htmlspecialchars($medicoSel['especialidad'] ?? '') ?></p>
      </div>
    </div>
    <?php endif; ?>

    <label class="block text-sm font-semibold text-slate-700 mb-2">¿Qué día preferís?</label>
    <input type="date" id="fechaInput"
           min="<?= date('Y-m-d') ?>"
           max="<?= date('Y-m-d', strtotime('+60 days')) ?>"
           class="input-field w-full md:w-64">
    <p class="text-slate-400 text-xs mt-2"><i class="fas fa-info-circle mr-1"></i>Podés reservar hasta 60 días de anticipación.</p>
  </div>

  <?php elseif (!isset($_GET['hora'])): ?>
  <?php
  $medicoNoDisponible = !empty($slots) && isset($slots[0]['hora']) && $slots[0]['hora'] === null;
  ?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
    <div class="flex items-center gap-3 mb-6">
      <a href="<?= APP_URL ?>/agendar?especialidad_id=<?= htmlspecialchars($_GET['especialidad_id']) ?>&medico_id=<?= htmlspecialchars($_GET['medico_id']) ?>"
         class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
        <i class="fas fa-arrow-left text-xs"></i>
      </a>
      <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
        <span class="step-badge">4</span> Seleccioná el Horario
      </h2>
    </div>

    <?php if (!is_null($medicoSel)): ?>
    <p class="text-slate-500 text-sm mb-5">
      Dr. <?= htmlspecialchars($medicoSel['nombre'] . ' ' . $medicoSel['apellido']) ?> —
      <span class="font-semibold text-slate-700"><?= date('d/m/Y', strtotime($_GET['fecha'])) ?></span>
    </p>
    <?php endif; ?>

    <?php if ($medicoNoDisponible): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center text-red-700">
      <p class="font-semibold"><?= htmlspecialchars($slots[0]['mensaje'] ?? 'El médico no está disponible.') ?></p>
    </div>
    <?php elseif (empty($slots)): ?>
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 text-center text-orange-700">
      <i class="fas fa-calendar-times text-3xl mb-2 block opacity-50"></i>
      <p class="font-semibold">No hay horarios disponibles para este día.</p>
      <p class="text-sm mt-1">El médico no atiende este día. Probá con otra fecha.</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-2 sm:gap-3">
      <?php foreach ($slots as $slot): ?>
      <button type="button"
              <?= $slot['disponible'] ? "onclick=\"seleccionarSlot('{$slot['hora']}')\"" : 'disabled' ?>
              class="py-2 sm:py-3 px-1 sm:px-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-semibold text-center transition
              <?= $slot['disponible'] ? 'slot-available' : 'slot-taken' ?>">
        <?= htmlspecialchars($slot['hora']) ?>
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php else: ?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
    <form method="POST" action="<?= APP_URL ?>/cita/guardar" id="mainForm" class="space-y-5" data-loading data-validate>
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input type="hidden" name="especialidad_id" value="<?= (int)($_GET['especialidad_id'] ?? 0) ?>">
      <input type="hidden" name="medico_id" value="<?= (int)($_GET['medico_id'] ?? 0) ?>">
      <input type="hidden" name="fecha" value="<?= htmlspecialchars($_GET['fecha']) ?>">
      <input type="hidden" name="hora" value="<?= htmlspecialchars($_GET['hora']) ?>">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre Completo *</label>
          <input type="text" name="nombre_paciente" required class="input-field" placeholder="Ej: Juan Pérez">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono *</label>
          <input type="tel" name="telefono" required class="input-field" placeholder="0981 000 000">
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Correo Electrónico</label>
        <input type="email" name="email" class="input-field" placeholder="correo@ejemplo.com">
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Motivo de la Consulta *</label>
        <textarea name="motivo" required rows="3" class="input-field resize-none" placeholder="Describí brevemente el motivo..."></textarea>
      </div>

      <div class="flex flex-col md:flex-row gap-3 pt-2">
        <button type="submit"
                class="flex-1 py-4 rounded-xl text-white font-bold text-base shadow-lg hover:shadow-xl transition-all"
                style="background:linear-gradient(135deg,#0284c7,#0e7490)">
          Confirmar Cita
        </button>
        <a href="<?= APP_URL ?>/"
           class="flex-1 py-4 rounded-xl bg-slate-100 text-slate-600 font-bold text-base hover:bg-slate-200 transition text-center">
          Cancelar
        </a>
      </div>
    </form>
  </div>
  <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const fechaInput = document.getElementById('fechaInput');
  if (fechaInput) {
    fechaInput.addEventListener('change', function () {
      if (this.value) {
        location.href = '<?= APP_URL ?>/agendar?especialidad_id=<?= urlencode($_GET['especialidad_id'] ?? '') ?>&medico_id=<?= urlencode($_GET['medico_id'] ?? '') ?>&fecha=' + this.value;
      }
    });
  }
});

function seleccionarSlot(hora) {
  const url = new URL(window.location.href);
  url.searchParams.set('hora', hora);
  window.location.href = url.toString();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>