<?php require_once __DIR__ . '/../layouts/header_public.php'; ?>

<!-- ─── HERO ─── -->
<section class="relative overflow-hidden"
         style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 40%,#0e7490 100%)">
  <!-- BG Orbs -->
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full opacity-20"
         style="background:radial-gradient(circle,#38bdf8 0%,transparent 70%)"></div>
    <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full opacity-10"
         style="background:radial-gradient(circle,#0ea5e9 0%,transparent 70%)"></div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <!-- Left content -->
      <div class="fade-up">
        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-200 text-xs font-semibold px-4 py-1.5 rounded-full mb-5">
          <i class="fas fa-shield-alt"></i> Sistema seguro de turnos online
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-5">
          Agenda tu cita<br>
          <mark style="background:rgba(56,189,248,0.18);color:#7dd3fc;padding:0 0.2em;border-radius:0.2em;-webkit-box-decoration-break:clone;box-decoration-break:clone">médica en minutos</mark>
        </h1>
        <p class="text-slate-300 text-lg mb-8 max-w-lg">
          Sin filas, sin llamadas. Elegí especialidad, médico y horario de forma rápida y segura desde cualquier dispositivo.
        </p>
        <div class="flex flex-wrap gap-3">
          <a href="<?= APP_URL ?>/agendar"
             class="inline-flex items-center gap-2 px-8 py-4 rounded-full text-base font-bold text-white shadow-xl hover:shadow-2xl transition-all hover:scale-105"
             style="background:linear-gradient(135deg,#0284c7,#0e7490)">
            <i class="fas fa-calendar-check"></i> Agendar Cita Ahora
          </a>
          <a href="#especialidades"
             class="inline-flex items-center gap-2 px-8 py-4 rounded-full text-base font-semibold text-white/80 border border-white/20 hover:bg-white/10 transition-all">
            Ver Especialidades <i class="fas fa-arrow-right text-xs"></i>
          </a>
        </div>

        <!-- Stats row -->
        <div class="flex items-center gap-8 mt-12 pt-8 border-t border-white/10">
          <div>
            <p class="text-2xl font-extrabold text-white">+500</p>
            <p class="text-sky-200 text-xs">Citas realizadas</p>
          </div>
          <div>
            <p class="text-2xl font-extrabold text-white">+50</p>
            <p class="text-sky-200 text-xs">Médicos activos</p>
          </div>
          <div>
            <p class="text-2xl font-extrabold text-white">15</p>
            <p class="text-sky-200 text-xs">Especialidades</p>
          </div>
        </div>
      </div>

      <!-- Right illustration -->
      <div class="hidden lg:flex justify-center relative fade-up" style="animation-delay:.15s">
        <div class="relative">
          <!-- Main card -->
          <div class="bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-white/10 shadow-2xl w-80">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                   style="background:linear-gradient(135deg,#0284c7,#0e7490)">
                <i class="fas fa-calendar-check text-white text-lg"></i>
              </div>
              <div>
                <p class="text-white font-bold text-sm">Próxima Cita</p>
                <p class="text-sky-200 text-xs">Confirmada</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                <i class="fas fa-user text-sky-300"></i>
                <div>
                  <p class="text-white text-xs font-semibold">María González</p>
                  <p class="text-sky-200 text-[10px]">Paciente</p>
                </div>
              </div>
              <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                <i class="fas fa-user-md text-sky-300"></i>
                <div>
                  <p class="text-white text-xs font-semibold">Dr. Carlos Martínez</p>
                  <p class="text-sky-200 text-[10px]">Cardiología</p>
                </div>
              </div>
              <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5">
                <i class="fas fa-clock text-sky-300"></i>
                <div>
                  <p class="text-white text-xs font-semibold">15 Jul 2026 · 10:30</p>
                  <p class="text-sky-200 text-[10px]">Consulta</p>
                </div>
              </div>
            </div>
          </div>
          <!-- Floating badge -->
          <div class="absolute -top-4 -right-4 bg-emerald-500 rounded-full px-4 py-2 shadow-lg">
            <p class="text-white text-xs font-bold flex items-center gap-1">
              <i class="fas fa-check-circle"></i> Confirmado
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Wave -->
  <div class="absolute bottom-0 left-0 right-0 leading-none">
    <svg viewBox="0 0 1200 80" preserveAspectRatio="none" class="w-full h-12 md:h-16 fill-slate-50">
      <path d="M0,40 C300,80 900,0 1200,40 L1200,80 L0,80 Z"/>
    </svg>
  </div>
</section>

<!-- ─── BENEFITS ─── -->
<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-14 fade-up">
      <span class="text-sky-600 font-bold text-sm tracking-widest uppercase">Beneficios</span>
      <h2 class="text-3xl font-extrabold text-slate-800 mt-2">¿Por qué elegirnos?</h2>
      <p class="text-slate-400 mt-2 max-w-lg mx-auto">Simplificamos el proceso de agendamiento para que te enfoques en tu salud</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
      $benefits = [
        ['fa-clock', 'Sin Esperas', 'Agendá tu cita en menos de 2 minutos. Olvidate de las largas filas y las llamadas telefónicas.'],
        ['fa-shield-alt', 'Seguro y Confiable', 'Tus datos están protegidos. Sistema seguro con confirmación por email y recordatorios.'],
        ['fa-mobile-alt', 'Desde Cualquier Lugar', 'Accedé desde tu celular, tablet o computadora. Disponible 24/7.'],
        ['fa-calendar-alt', 'Gestión de Turnos', 'Elegí el día y horario que mejor se adapte a tu agenda. Cancelá o reprogramá fácilmente.'],
        ['fa-user-md', 'Mejores Profesionales', 'Contamos con un equipo médico de primer nivel en diversas especialidades.'],
        ['fa-heartbeat', 'Cuidado Integral', 'Atención personalizada para vos y tu familia. Tu salud es nuestra prioridad.'],
      ];
      foreach ($benefits as $b): ?>
      <div class="card p-6 text-center hover:shadow-lg transition-all duration-200 fade-up group">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 transition-colors group-hover:bg-sky-100"
             style="background:linear-gradient(135deg,#e0f2fe,#bae6fd)">
          <i class="fas <?= $b[0] ?> text-sky-600 text-xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1.5"><?= $b[1] ?></h3>
        <p class="text-slate-500 text-sm leading-relaxed"><?= $b[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ─── CÓMO FUNCIONA ─── -->
<section class="py-20 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-14 fade-up">
      <span class="text-sky-600 font-bold text-sm tracking-widest uppercase">Proceso simple</span>
      <h2 class="text-3xl font-extrabold text-slate-800 mt-2">¿Cómo funciona?</h2>
      <p class="text-slate-400 mt-2">Agendá tu cita en 4 pasos simples</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php
      $pasos = [
        ['fa-stethoscope', '1', 'Elige Especialidad', 'Seleccioná la especialidad médica que necesitás'],
        ['fa-user-md',     '2', 'Seleccioná Médico',  'Elegí al profesional de tu preferencia'],
        ['fa-calendar-alt','3', 'Fecha y Horario',    'Seleccioná el día y el turno disponible'],
        ['fa-check-circle','4', 'Confirmá tu Cita',   'Completá tus datos y recibí la confirmación'],
      ];
      foreach ($pasos as $paso): ?>
      <div class="bg-white rounded-2xl p-7 border border-slate-100 shadow-sm text-center relative fade-up card-hover">
        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
          <div class="w-8 h-8 rounded-full text-white text-sm font-bold flex items-center justify-center shadow-md"
               style="background:linear-gradient(135deg,#0284c7,#0e7490)">
            <?= $paso[1] ?>
          </div>
        </div>
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 mt-3"
             style="background:linear-gradient(135deg,#e0f2fe,#bae6fd)">
          <i class="fas <?= $paso[0] ?> text-sky-600 text-xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 mb-1 text-base"><?= $paso[2] ?></h3>
        <p class="text-slate-500 text-sm"><?= $paso[3] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ─── ESPECIALIDADES ─── -->
<?php if (!empty($especialidades)): ?>
<section id="especialidades" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-14 fade-up">
      <span class="text-sky-600 font-bold text-sm tracking-widest uppercase">Cobertura médica</span>
      <h2 class="text-3xl font-extrabold text-slate-800 mt-2">Nuestras Especialidades</h2>
      <p class="text-slate-400 mt-2">Contamos con profesionales en diversas áreas de la medicina</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
      <?php
      $espColors = ['#0284c7','#7c3aed','#0891b2','#059669','#d97706','#dc2626'];
      $idx = 0;
      foreach ($especialidades as $esp):
        $color = $espColors[$idx % 6]; $idx++;
      ?>
      <a href="<?= APP_URL ?>/agendar?especialidad_id=<?= $esp['id'] ?>"
         class="bg-slate-50 hover:bg-sky-50 border border-slate-100 hover:border-sky-200 rounded-2xl p-5 text-center transition-all duration-200 group">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3"
             style="background:linear-gradient(135deg,#e0f2fe,#bae6fd)">
          <i class="fas <?= htmlspecialchars($esp['icono'] ?? 'fa-stethoscope') ?> text-xl" style="color:<?= $color ?>"></i>
        </div>
        <p class="font-semibold text-slate-700 text-xs leading-tight"><?= htmlspecialchars($esp['nombre']) ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── MÉDICOS ─── -->
<?php if (!empty($medicos)): ?>
<section id="medicos" class="py-20 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-14 fade-up">
      <span class="text-sky-600 font-bold text-sm tracking-widest uppercase">Profesionales</span>
      <h2 class="text-3xl font-extrabold text-slate-800 mt-2">Nuestro Equipo Médico</h2>
      <p class="text-slate-400 mt-2">Conocé a los profesionales que te atenderán</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach (array_slice($medicos, 0, 6) as $m): ?>
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden card-hover fade-up">
        <div class="h-20 relative" style="background:linear-gradient(135deg,#0f172a,#0e7490)">
          <div class="absolute -bottom-6 left-6">
            <div class="w-14 h-14 rounded-2xl border-4 border-white flex items-center justify-center text-white text-xl shadow-md"
                 style="background:linear-gradient(135deg,#0284c7,#0e7490)">
              <i class="fas fa-user-md"></i>
            </div>
          </div>
        </div>
        <div class="pt-10 px-6 pb-6">
          <h3 class="font-bold text-slate-800">Dr. <?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido']) ?></h3>
          <p class="text-sky-600 text-sm font-medium mb-4"><?= htmlspecialchars($m['especialidad'] ?? '') ?></p>
          <a href="<?= APP_URL ?>/agendar?medico_id=<?= $m['id'] ?>&especialidad_id=<?= $m['especialidad_id'] ?>"
             class="block w-full text-center py-2.5 rounded-xl text-sm font-semibold text-white transition-all"
             style="background:linear-gradient(135deg,#0284c7,#0e7490)">
            <i class="fas fa-calendar-plus mr-1.5"></i>Agendar Cita
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ─── FAQ ─── -->
<section class="py-20 bg-white">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-14 fade-up">
      <span class="text-sky-600 font-bold text-sm tracking-widest uppercase">FAQ</span>
      <h2 class="text-3xl font-extrabold text-slate-800 mt-2">Preguntas Frecuentes</h2>
    </div>
    <div class="space-y-3">
      <?php
      $faqs = [
        ['¿Cómo agendo una cita?', 'Simplemente seleccioná la especialidad, elegí un médico disponible, escogé el horario que mejor te convenga y completá tus datos. Recibirá una confirmación por correo electrónico.'],
        ['¿Puedo cancelar mi cita?', 'Sí, podés cancelar tu cita usando el enlace que recibiste en el correo de confirmación. También podés hacerlo desde el panel de administración si tenés acceso.'],
        ['¿El servicio tiene algún costo?', 'El sistema de agendamiento es completamente gratuito. Los costos de la consulta médica dependen de cada profesional y deben ser consultados directamente.'],
        ['¿Qué necesito para agendar?', 'Solamente necesitás tener acceso a internet y un correo electrónico válido para recibir la confirmación. No requiere registro ni crear una cuenta.'],
        ['¿Puedo agendar para otra persona?', 'Sí, podés agendar citas para familiares. Simplemente ingresá los datos del paciente al momento de completar el formulario de agendamiento.'],
      ];
      foreach ($faqs as $i => $faq): ?>
      <div class="border border-slate-100 rounded-2xl overflow-hidden fade-up">
        <button type="button" class="w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-slate-800 hover:bg-slate-50 transition text-sm"
                onclick="this.nextElementSibling.classList.toggle('hidden');this.querySelector('i').classList.toggle('fa-chevron-down');this.querySelector('i').classList.toggle('fa-chevron-up')">
          <?= $faq[0] ?>
          <i class="fas fa-chevron-down text-slate-300 text-xs transition"></i>
        </button>
        <div class="px-5 pb-4 text-sm text-slate-500 leading-relaxed hidden">
          <?= $faq[1] ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ─── CTA FINAL ─── -->
<section class="py-20"
         style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0e7490 100%)">
  <div class="max-w-3xl mx-auto px-4 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl"
         style="background:linear-gradient(135deg,#e0f2fe,#bae6fd)">
      <i class="fas fa-calendar-check text-sky-600 text-2xl"></i>
    </div>
    <h2 class="text-3xl font-extrabold text-white mb-3">¿Listo para agendar?</h2>
    <p class="text-sky-200 text-lg mb-8">El proceso tarda menos de 2 minutos. Sin registrarse.</p>
    <a href="<?= APP_URL ?>/agendar"
       class="inline-flex items-center gap-2 px-10 py-4 rounded-full text-base font-bold text-white shadow-xl hover:shadow-2xl transition-all hover:scale-105"
       style="background:linear-gradient(135deg,#0284c7,#0e7490)">
      <i class="fas fa-arrow-right"></i> Comenzar Ahora
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
