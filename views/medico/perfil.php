<?php
/**
 * @var array $medico
 * @var array $especialidades
 * @var string $csrfToken
 */
require_once __DIR__ . '/../layouts/header_admin.php';
?>

<div class="max-w-2xl mx-auto fade-up">
  <div class="flex items-center gap-2 text-sm mb-6">
    <a href="<?= APP_URL ?>/medico/dashboard" class="text-slate-400 hover:text-sky-600 transition">Dashboard</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-700 font-semibold">Mi Perfil</span>
  </div>

  <div class="card">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold"
           style="background:linear-gradient(135deg,#0284c7,#0e7490)">
        <?= strtoupper(substr($medico['nombre'] ?? 'D', 0, 1) . substr($medico['apellido'] ?? 'R', 0, 1)) ?>
      </div>
      <div>
        <h1 class="text-lg font-extrabold text-slate-800"><?= htmlspecialchars($medico['nombre'] . ' ' . $medico['apellido']) ?></h1>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($medico['especialidad'] ?? '') ?></p>
      </div>
      <span class="badge badge-<?= $medico['disponible'] ? 'success' : 'secondary' ?> ml-auto">
        <?= $medico['disponible'] ? 'Disponible' : 'No disponible' ?>
      </span>
    </div>

    <form method="POST" action="<?= APP_URL ?>/medico/perfil/actualizar" class="p-5 space-y-4" data-loading>
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <div class="grid grid-cols-2 gap-3">
        <div class="floating-label">
          <input type="text" name="nombre" required value="<?= htmlspecialchars($medico['nombre'] ?? '') ?>">
          <label>Nombre</label>
        </div>
        <div class="floating-label">
          <input type="text" name="apellido" required value="<?= htmlspecialchars($medico['apellido'] ?? '') ?>">
          <label>Apellido</label>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="floating-label">
          <input type="email" name="email" value="<?= htmlspecialchars($medico['email'] ?? '') ?>">
          <label>Email</label>
        </div>
        <div class="floating-label">
          <input type="text" name="telefono" value="<?= htmlspecialchars($medico['telefono'] ?? '') ?>">
          <label>Teléfono</label>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="floating-label">
          <input type="text" name="matricula" value="<?= htmlspecialchars($medico['matricula'] ?? '') ?>">
          <label>Matrícula</label>
        </div>
        <div class="floating-label">
          <input type="text" value="<?= htmlspecialchars($medico['especialidad'] ?? '') ?>" disabled class="text-slate-400">
          <label>Especialidad</label>
        </div>
      </div>

      <div class="floating-label">
        <textarea name="descripcion" rows="3"><?= htmlspecialchars($medico['descripcion'] ?? '') ?></textarea>
        <label>Descripción / Biografía</label>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Guardar Cambios
        </button>
        <a href="<?= APP_URL ?>/medico/dashboard" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
