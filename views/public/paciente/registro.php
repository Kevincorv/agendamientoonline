<?php require_once __DIR__ . '/../../layouts/header_public.php'; ?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
  <div class="w-full max-w-md fade-up">
    <div class="text-center mb-8">
      <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white mx-auto mb-4"
           style="background:linear-gradient(135deg,#059669,#10b981)">
        <i class="fas fa-user-plus text-2xl"></i>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-800">Crear Cuenta</h1>
      <p class="text-sm text-slate-500 mt-1">Registrate para agendar y gestionar tus citas</p>
    </div>

    <div class="card p-6">
      <form method="POST" action="<?= APP_URL ?>/paciente/registro" data-loading>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="grid grid-cols-2 gap-3 mb-4">
          <div class="floating-label">
            <input type="text" name="nombre" required autocomplete="given-name">
            <label>Nombre</label>
          </div>
          <div class="floating-label">
            <input type="text" name="apellido" required autocomplete="family-name">
            <label>Apellido</label>
          </div>
        </div>

        <div class="floating-label mb-4">
          <input type="email" name="email" required autocomplete="email" inputmode="email">
          <label>Correo electrónico</label>
        </div>

        <div class="floating-label mb-4">
          <input type="password" name="password" required minlength="6" autocomplete="new-password">
          <label>Contraseña (mín. 6 caracteres)</label>
        </div>

        <div class="floating-label mb-6">
          <input type="password" name="password_confirm" required minlength="6" autocomplete="new-password">
          <label>Confirmar contraseña</label>
        </div>

        <button type="submit" class="btn btn-primary w-full">
          <i class="fas fa-user-check"></i> Crear Cuenta
        </button>
      </form>

      <div class="mt-6 text-center">
        <a href="<?= APP_URL ?>/paciente/login" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition inline-flex items-center gap-1">
          <i class="fas fa-sign-in-alt"></i> ¿Ya tenés cuenta? Iniciá sesión
        </a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
