<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>
<div class="p-4 sm:p-6 lg:p-7 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">
          <i class="fas fa-stethoscope text-sky-500 mr-2"></i>Mi Agenda
          <span class="text-slate-500 text-base font-semibold">— Dr. <?= htmlspecialchars($medico['nombre'] . ' ' . $medico['apellido']) ?></span>
        </h1>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="fecha" value="<?= $fecha ?>"
                   class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-sky-400">
            <button type="submit"
                    class="btn-primary">
              <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <div class="card overflow-hidden fade-up">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
          <h2 class="font-bold text-slate-700">Citas del <?= formatearFecha($fecha) ?></h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm resp-table">
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
                <?php if (empty($citas)): ?>
                <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400" data-label="">No hay citas para esta fecha</td></tr>
                <?php else: ?>
                <?php foreach ($citas as $c): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-bold text-sky-600 text-lg" data-label="Hora"><?= substr($c['hora'] ?? '', 0, 5) ?></td>
                    <td class="px-4 py-3 font-medium text-slate-800" data-label="Paciente"><?= htmlspecialchars($c['nombre_paciente']) ?></td>
                    <td class="px-4 py-3 text-slate-500 text-xs" data-label="Contacto">
                      <p><?= htmlspecialchars($c['telefono']) ?></p>
                      <?php if ($c['email']): ?><p><?= htmlspecialchars($c['email']) ?></p><?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs" data-label="Motivo"><?= htmlspecialchars(mb_strimwidth($c['motivo'] ?? '', 0, 40, '...')) ?></td>
                    <td class="px-4 py-3" data-label="Estado">
                      <?php
                        $mapa = ['pendiente'=>'badge-yellow','confirmada'=>'badge-green','cancelada'=>'badge-red','completada'=>'badge-blue','no_asistio'=>'badge-red'];
                        $bc = $mapa[$c['estado']] ?? 'badge-gray';
                      ?>
                      <span class="badge <?= $bc ?>"><?= ucfirst(str_replace('_',' ',$c['estado'])) ?></span>
                    </td>
                    <td class="px-4 py-3" data-label="Acción">
                        <form method="POST" action="<?= APP_URL ?>/admin/citas/estado">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <input type="hidden" name="cita_id" value="<?= $c['id'] ?>">
                            <select name="estado_id" onchange="this.form.submit()"
                                    class="text-xs border border-slate-200 rounded px-2 py-1.5 focus:outline-none cursor-pointer">
                                <option value="">Cambiar...</option>
                                <option value="4">Atendida</option>
                                <option value="5">No asistió</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
      </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
