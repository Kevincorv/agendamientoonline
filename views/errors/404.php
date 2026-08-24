<?php require_once __DIR__ . '/../layouts/header_public.php'; ?>

<div class="max-w-lg mx-auto px-4 py-24 text-center fade-up">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12">
    <p class="text-8xl font-black text-sky-100 mb-4 leading-none">404</p>
    <h1 class="text-2xl font-extrabold text-slate-800 mb-3">Página no encontrada</h1>
    <p class="text-slate-500 mb-8">La ruta que buscás no existe o fue movida.</p>
    <a href="<?= APP_URL ?>/"
       class="inline-flex items-center gap-2 text-white font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition-all"
       style="background:linear-gradient(135deg,#0284c7,#0e7490)">
      <i class="fas fa-home"></i> Volver al Inicio
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
