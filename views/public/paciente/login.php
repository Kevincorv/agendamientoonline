<?php require_once __DIR__ . '/../../layouts/header_public.php'; ?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4"
     style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 50%,#f0fdf4 100%)">
  <div class="w-full max-w-md fade-up">
    <!-- Logo -->
    <div class="text-center mb-8">
      <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl ring-4 ring-white/50"
           style="background:linear-gradient(135deg,#0284c7,#0e7490)">
        <i class="fas fa-user-injured text-white text-3xl"></i>
      </div>
      <h1 class="text-2xl font-extrabold text-slate-800">Portal del Paciente</h1>
      <p class="text-sm text-slate-500 mt-1">Ingresá con tu cuenta para gestionar tus citas</p>
    </div>

    <!-- Card -->
    <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl shadow-sky-100/50 border border-white/60 p-8">
      <form method="POST" action="<?= APP_URL ?>/paciente/login" data-loading class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="floating-label">
          <input type="email" name="email" id="email" class="input-field" required autocomplete="email" inputmode="email" placeholder=" ">
          <label for="email">Correo electrónico</label>
        </div>

        <div class="floating-label">
          <input type="password" name="password" id="password" class="input-field" required autocomplete="current-password" placeholder=" ">
          <label for="password">Contraseña</label>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
            <span class="text-slate-500">Recordarme</span>
          </label>
          <a href="<?= APP_URL ?>/paciente/registro" class="font-semibold text-sky-600 hover:text-sky-700 transition">
            ¿No tenés cuenta?
          </a>
        </div>

        <button type="submit" class="btn btn-primary w-full py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all">
          <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-slate-100 text-center">
        <a href="<?= APP_URL ?>/"
           class="text-slate-400 hover:text-sky-600 transition inline-flex items-center gap-2 text-sm font-medium">
          <i class="fas fa-arrow-left"></i> Volver al inicio
        </a>
        <span class="mx-3 text-slate-200">|</span>
        <a href="<?= APP_URL ?>/admin"
           class="text-slate-400 hover:text-sky-600 transition inline-flex items-center gap-2 text-sm font-medium">
          <i class="fas fa-lock"></i> Acceso personal
        </a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
