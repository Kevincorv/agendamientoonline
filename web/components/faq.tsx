"use client";

import { useState } from "react";

const FAQS: Array<[string, string]> = [
  [
    "¿Cómo agendo una cita?",
    "Simplemente seleccioná la especialidad, elegí un médico disponible, escogé el horario que mejor te convenga y completá tus datos. Recibirá una confirmación por correo electrónico.",
  ],
  [
    "¿Puedo cancelar mi cita?",
    "Sí, podés cancelar tu cita usando el enlace que recibiste en el correo de confirmación. También podés hacerlo desde el panel de administración si tenés acceso.",
  ],
  [
    "¿El servicio tiene algún costo?",
    "El sistema de agendamiento es completamente gratuito. Los costos de la consulta médica dependen de cada profesional y deben ser consultados directamente.",
  ],
  [
    "¿Qué necesito para agendar?",
    "Solamente necesitás tener acceso a internet y un correo electrónico válido para recibir la confirmación. No requiere registro ni crear una cuenta.",
  ],
  [
    "¿Puedo agendar para otra persona?",
    "Sí, podés agendar citas para familiares. Simplemente ingresá los datos del paciente al momento de completar el formulario de agendamiento.",
  ],
];

export function FaqList() {
  const [open, setOpen] = useState<number | null>(null);
  return (
    <div className="space-y-3">
      {FAQS.map(([q, a], i) => {
        const isOpen = open === i;
        return (
          <div key={i} className="border border-slate-100 rounded-2xl overflow-hidden fade-up">
            <button
              type="button"
              onClick={() => setOpen(isOpen ? null : i)}
              className="w-full flex items-center justify-between px-5 py-4 text-left font-semibold text-slate-800 hover:bg-slate-50 transition text-sm"
            >
              {q}
              <i className={`fas ${isOpen ? "fa-chevron-up" : "fa-chevron-down"} text-slate-300 text-xs transition`}></i>
            </button>
            <div className={`px-5 pb-4 text-sm text-slate-500 leading-relaxed ${isOpen ? "" : "hidden"}`}>{a}</div>
          </div>
        );
      })}
    </div>
  );
}
