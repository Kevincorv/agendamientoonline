<?php
/**
 * @var array $medicos
 * @var array $horariosPorMedico
 * @var array $bloqueosPorMedico
 * @var string $csrfToken
 */
$dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
?>
<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="fade-up">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-800">Horarios</h1>
      <p class="text-sm text-slate-500 mt-0.5">Configuración de bloques horarios por médico y duración de consulta</p>
    </div>
  </div>

  <?php if (empty($medicos)): ?>
    <?php require_once __DIR__ . '/../components/table_empty.php'; ?>
  <?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
      <?php foreach ($medicos as $medico):
        $bloques = $horariosPorMedico[$medico['id']] ?? [];
        $bloqueos = $bloqueosPorMedico[$medico['id']] ?? [];
        $initial = strtoupper(substr($medico['nombre'], 0, 1) . substr($medico['apellido'], 0, 1));
      ?>
      <div class="card overflow-hidden">
        <!-- Card Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
               style="background:linear-gradient(135deg,#0284c7,#0e7490)">
            <?= $initial ?>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-slate-800 truncate">
              <?= htmlspecialchars($medico['nombre'] . ' ' . $medico['apellido']) ?>
            </p>
            <p class="text-xs text-slate-500"><?= htmlspecialchars($medico['especialidad'] ?? '') ?></p>
          </div>
          <span class="badge badge-<?= $medico['disponible'] ? 'success' : 'secondary' ?> text-[10px]">
            <?= $medico['disponible'] ? 'Disponible' : 'No disponible' ?>
          </span>
        </div>

        <!-- Bloques actuales -->
        <div class="px-5 py-3">
          <?php if (empty($bloques)): ?>
            <p class="text-xs text-slate-400 text-center py-4">
              <i class="far fa-clock mr-1"></i> Sin bloques configurados
            </p>
          <?php else: ?>
            <table class="w-full text-xs">
              <thead>
                <tr class="text-slate-400 font-semibold uppercase tracking-wider">
                  <th class="text-left pb-2">Día</th>
                  <th class="text-left pb-2">Inicio</th>
                  <th class="text-left pb-2">Fin</th>
                  <th class="text-left pb-2">Duración</th>
                  <th class="pb-2"></th>
                </tr>
              </thead>
              <tbody>
                <?php $agrupados = []; foreach ($bloques as $b) $agrupados[$b['dia_semana']][] = $b; ?>
                <?php foreach ($agrupados as $dia => $bloquesDia): ?>
                  <?php foreach ($bloquesDia as $i => $b): ?>
                  <tr class="border-t border-slate-50">
                    <?php if ($i === 0): ?>
                    <td class="py-2 font-semibold text-slate-700" rowspan="<?= count($bloquesDia) ?>">
                      <?= $dias[(int)$b['dia_semana']] ?>
                    </td>
                    <?php endif; ?>
                    <td class="py-2 text-slate-600"><?= $b['hora_inicio'] ?></td>
                    <td class="py-2 text-slate-600"><?= $b['hora_fin'] ?></td>
                    <td class="py-2 text-slate-600"><?= (int)($b['duracion'] ?? $b['intervalo_minutos'] ?? 30) ?> min</td>
                    <td class="py-2 text-right">
                      <form method="POST" action="<?= APP_URL ?>/admin/horarios/eliminar"
                            onsubmit="return confirm('¿Eliminar este bloque?')">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                        <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Eliminar bloque">
                          <i class="fas fa-times"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>

        <!-- Add block form -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
          <button type="button" onclick="document.getElementById('addBlock_<?= $medico['id'] ?>').classList.toggle('hidden')"
                  class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition">
            <i class="fas fa-plus mr-1"></i> Agregar bloque
          </button>

          <!-- Bloqueos existentes -->
          <?php if (!empty($bloqueos)): ?>
            <div class="px-5 py-2 border-t border-slate-100 bg-red-50/30">
              <p class="text-[10px] font-semibold text-red-400 uppercase mb-1">Fechas bloqueadas</p>
              <?php foreach ($bloqueos as $b): ?>
              <div class="flex items-center justify-between text-xs py-0.5">
                <span class="text-slate-600"><?= formatearFecha($b['fecha']) ?>
                  <?php if ($b['motivo']): ?><span class="text-slate-400">— <?= htmlspecialchars($b['motivo']) ?></span><?php endif; ?>
                </span>
                <form method="POST" action="<?= APP_URL ?>/admin/horarios/desbloquear" class="inline">
                  <input type="hidden" name="id" value="<?= $b['id'] ?>">
                  <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Desbloquear">
                    <i class="fas fa-unlock-alt text-[10px]"></i>
                  </button>
                </form>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Bloqueo de fecha -->
          <div class="mt-3 pt-3 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('blockDate_<?= $medico['id'] ?>').classList.toggle('hidden')"
                    class="text-xs font-semibold text-red-500 hover:text-red-600 transition">
              <i class="fas fa-ban mr-1"></i> Bloquear fecha
            </button>

            <form id="blockDate_<?= $medico['id'] ?>" method="POST" action="<?= APP_URL ?>/admin/horarios/bloquear"
                  class="hidden mt-3 space-y-2">
              <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
              <input type="hidden" name="medico_id" value="<?= $medico['id'] ?>">
              <div class="grid grid-cols-2 gap-2">
                <div class="floating-label">
                  <input type="date" name="fecha" required min="<?= date('Y-m-d') ?>">
                  <label>Fecha</label>
                </div>
                <div class="floating-label">
                  <input type="text" name="motivo" placeholder="Vacaciones, licencia..." maxlength="200">
                  <label>Motivo</label>
                </div>
              </div>
              <button type="submit" class="btn btn-danger btn-sm w-full">
                <i class="fas fa-lock"></i> Bloquear
              </button>
            </form>
          </div>

          <form id="addBlock_<?= $medico['id'] ?>" method="POST" action="<?= APP_URL ?>/admin/horarios/crear"
                class="hidden mt-3 space-y-2" data-loading>
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="medico_id" value="<?= $medico['id'] ?>">

            <div class="grid grid-cols-2 gap-2">
              <div class="floating-label">
                <select name="dia_semana" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($dias as $idx => $label): ?>
                  <option value="<?= $idx ?>"><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
                <label>Día</label>
              </div>
              <div class="floating-label">
                <select name="duracion" required>
                  <option value="15">15 min</option>
                  <option value="20">20 min</option>
                  <option value="30" selected>30 min</option>
                  <option value="45">45 min</option>
                  <option value="60">60 min</option>
                </select>
                <label>Duración</label>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div class="floating-label">
                <input type="time" name="hora_inicio" required>
                <label>Inicio</label>
              </div>
              <div class="floating-label">
                <input type="time" name="hora_fin" required>
                <label>Fin</label>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm w-full">
              <i class="fas fa-save"></i> Guardar
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
