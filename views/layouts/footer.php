</main>

<footer class="bg-white border-t border-slate-100 no-print">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
      <div class="sm:col-span-2 lg:col-span-1">
        <div class="flex items-center gap-2.5 mb-3">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white"
               style="background:linear-gradient(135deg,#0284c7,#0e7490)">
            <i class="fas fa-heartbeat text-sm"></i>
          </div>
          <div>
            <span class="font-extrabold text-slate-800 text-sm"><?= APP_NAME ?? 'Clínica' ?></span>
          </div>
        </div>
        <p class="text-xs text-slate-400 leading-relaxed max-w-xs">
          Sistema de gestión de citas médicas online. Agendá tu consulta de forma rápida, segura y sin filas.
        </p>
      </div>
      <div>
        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Navegación</h4>
        <ul class="space-y-2">
          <li><a href="<?= APP_URL ?>/" class="text-xs text-slate-400 hover:text-sky-600 transition">Inicio</a></li>
          <li><a href="<?= APP_URL ?>/agendar" class="text-xs text-slate-400 hover:text-sky-600 transition">Agendar Cita</a></li>
          <li><a href="<?= APP_URL ?>/#especialidades" class="text-xs text-slate-400 hover:text-sky-600 transition">Especialidades</a></li>
          <li><a href="<?= APP_URL ?>/#medicos" class="text-xs text-slate-400 hover:text-sky-600 transition">Médicos</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Accesos</h4>
        <ul class="space-y-2">
          <li><a href="<?= APP_URL ?>/admin" class="text-xs text-slate-400 hover:text-sky-600 transition">Panel Administrativo</a></li>
          <li><a href="<?= APP_URL ?>/admin" class="text-xs text-slate-400 hover:text-sky-600 transition">Iniciar Sesión</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Contacto</h4>
        <ul class="space-y-2">
          <li class="text-xs text-slate-400 flex items-center gap-2">
            <i class="fas fa-phone text-sky-500 w-3"></i> (021) 000-000
          </li>
          <li class="text-xs text-slate-400 flex items-center gap-2">
            <i class="fas fa-envelope text-sky-500 w-3"></i> info@clinica.com
          </li>
        </ul>
      </div>
    </div>
    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
      <p class="text-slate-400 text-xs">&copy; <?= date('Y') ?> <?= APP_NAME ?? 'Clínica' ?>. Todos los derechos reservados.</p>
      <p class="text-slate-300 text-xs">Hecho con <i class="fas fa-heart text-red-400"></i> para la comunidad</p>
    </div>
  </div>
</footer>

<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('<?= APP_URL ?>/public/sw.js');
}
</script>
</body>
</html>
