<?php
/**
 * @var array $usuario
 * @var string $csrfToken
 */
require_once __DIR__ . '/../../layouts/header_public.php';
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 fade-up">
  <div class="flex items-center gap-2 text-sm mb-6">
    <a href="<?= APP_URL ?>/paciente/dashboard" class="text-slate-400 hover:text-sky-600 transition">Inicio</a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-700 font-semibold">Mi Perfil</span>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Datos personales -->
    <div class="card">
      <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-bold text-slate-700">
          <i class="fas fa-user mr-2 text-sky-500"></i>Datos Personales
        </h2>
      </div>
      <form method="POST" action="<?= APP_URL ?>/paciente/perfil/actualizar" class="p-5 space-y-4" data-loading>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="grid grid-cols-2 gap-3">
          <div class="floating-label">
            <input type="text" name="nombre" required value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>">
            <label>Nombre</label>
          </div>
          <div class="floating-label">
            <input type="text" name="apellido" required value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>">
            <label>Apellido</label>
          </div>
        </div>

        <div class="floating-label">
          <input type="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" disabled class="text-slate-400">
          <label>Email</label>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
          <i class="fas fa-save"></i> Guardar Cambios
        </button>
      </form>
    </div>

    <!-- Cambiar contraseña -->
    <div class="card">
      <div class="px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-bold text-slate-700">
          <i class="fas fa-lock mr-2 text-amber-500"></i>Cambiar Contraseña
        </h2>
      </div>
      <form method="POST" action="<?= APP_URL ?>/paciente/password" class="p-5 space-y-4" data-loading>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="floating-label">
          <input type="password" name="password_actual" required autocomplete="current-password">
          <label>Contraseña actual</label>
        </div>
        <div class="floating-label">
          <input type="password" name="password_nueva" required minlength="6" autocomplete="new-password">
          <label>Nueva contraseña</label>
        </div>
        <div class="floating-label">
          <input type="password" name="password_confirmar" required minlength="6" autocomplete="new-password">
          <label>Confirmar nueva contraseña</label>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
          <i class="fas fa-key"></i> Cambiar Contraseña
        </button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
