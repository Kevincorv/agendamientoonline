"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useActionState, useEffect } from "react";
import { guardarCita } from "@/app/(public)/agendar/actions";

interface EspSimple {
  id: number;
  nombre: string;
  icono: string;
}
interface MedSimple {
  id: number;
  nombre: string;
  apellido: string;
  descripcion: string | null;
  especialidad_nombre: string | null;
}
interface MedSel {
  nombre: string;
  apellido: string;
  especialidad_nombre: string | null;
}
export interface SlotLite {
  hora: string | null;
  disponible: boolean;
  mensaje?: string;
}

interface Props {
  especialidades: EspSimple[];
  medicos: MedSimple[];
  medicoSel: MedSel | null;
  slots: SlotLite[];
  especialidadId: number;
  medicoId: number;
  fecha: string;
  hora: string;
  minDate: string;
  maxDate: string;
}

type Win = typeof window & { showToast?: (m: string, t?: "success" | "error" | "info") => void };

export function AgendarWizard(props: Props) {
  const router = useRouter();
  const [state, formAction, pending] = useActionState(guardarCita, undefined);

  useEffect(() => {
    if (state?.error) (window as Win).showToast?.(state.error, "error");
  }, [state]);

  const { especialidadId, medicoId, fecha, hora } = props;

  let step = 1;
  if (especialidadId) step = 2;
  if (medicoId) step = 3;
  if (fecha) step = 4;
  if (hora) step = 5;

  const steps = ["Especialidad", "Médico", "Fecha", "Horario", "Datos"];

  function nav(params: Record<string, string | number>) {
    const sp = new URLSearchParams();
    if (especialidadId) sp.set("especialidad_id", String(especialidadId));
    if (medicoId) sp.set("medico_id", String(medicoId));
    if (fecha) sp.set("fecha", fecha);
    for (const [k, v] of Object.entries(params)) sp.set(k, String(v));
    router.push(`/agendar?${sp.toString()}`);
  }

  const medicoNoDisponible =
    props.slots.length > 0 && props.slots[0].hora === null;

  return (
    <div className="max-w-4xl mx-auto px-4 py-12">
      <div className="text-center mb-10 fade-up">
        <h1 className="text-3xl font-extrabold text-slate-800">Agendar Cita Médica</h1>
        <p className="text-slate-500 mt-2">Seguí los pasos para reservar tu turno</p>
      </div>

      {/* Steps */}
      <div className="flex items-center justify-center gap-2 mb-10 fade-up">
        {steps.map((label, i) => {
          const n = i + 1;
          const done = n < step;
          const active = n === step;
          return (
            <div key={label} className="flex items-center gap-2">
              <div className="flex flex-col items-center">
                <div
                  className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all ${n <= step ? "text-white" : "bg-slate-100 text-slate-400"}`}
                  style={n <= step ? { background: "linear-gradient(135deg,#0284c7,#0e7490)" } : undefined}
                >
                  {done ? <i className="fas fa-check"></i> : n}
                </div>
                <span className={`text-xs mt-1 font-medium ${active ? "text-sky-600" : "text-slate-400"}`}>{label}</span>
              </div>
              {n < steps.length && (
                <div className={`w-8 h-0.5 mb-4 ${n < step ? "bg-sky-400" : "bg-slate-200"}`}></div>
              )}
            </div>
          );
        })}
      </div>

      {/* STEP 1: Especialidad */}
      {!especialidadId && (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
          <h2 className="font-bold text-slate-800 text-lg mb-6 flex items-center gap-2">
            <span className="step-badge">1</span> Seleccioná la Especialidad
          </h2>
          {props.especialidades.length === 0 ? (
            <p className="text-slate-400 text-center py-8">No hay especialidades disponibles.</p>
          ) : (
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
              {props.especialidades.map((esp) => (
                <Link
                  key={esp.id}
                  href={`/agendar?especialidad_id=${esp.id}`}
                  className="border-2 rounded-xl p-5 text-center transition-all card-hover border-slate-100 hover:border-sky-300 hover:bg-sky-50"
                >
                  <div
                    className="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3"
                    style={{ background: "linear-gradient(135deg,#e0f2fe,#bae6fd)" }}
                  >
                    <i className={`fas ${esp.icono || "fa-stethoscope"} text-sky-600 text-xl`}></i>
                  </div>
                  <p className="font-semibold text-slate-700 text-sm">{esp.nombre}</p>
                </Link>
              ))}
            </div>
          )}
        </div>
      )}

      {/* STEP 2: Médico */}
      {especialidadId && !medicoId && (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
          <div className="flex items-center gap-3 mb-6">
            <Link href="/agendar" className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
              <i className="fas fa-arrow-left text-xs"></i>
            </Link>
            <h2 className="font-bold text-slate-800 text-lg flex items-center gap-2">
              <span className="step-badge">2</span> Seleccioná el Médico
            </h2>
          </div>
          {props.medicos.length === 0 ? (
            <div className="bg-amber-50 border border-amber-200 rounded-xl p-5 text-center text-amber-700">
              <i className="fas fa-exclamation-triangle text-2xl mb-2 block"></i>
              No hay médicos disponibles para esta especialidad.
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {props.medicos.map((med) => (
                <Link
                  key={med.id}
                  href={`/agendar?especialidad_id=${especialidadId}&medico_id=${med.id}`}
                  className="border-2 rounded-xl p-5 flex items-center gap-4 transition-all card-hover"
                >
                  <div
                    className="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                    style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
                  >
                    {med.nombre.charAt(0).toUpperCase()}
                  </div>
                  <div>
                    <p className="font-bold text-slate-800">Dr. {med.nombre} {med.apellido}</p>
                    <p className="text-sky-600 text-xs font-medium">{med.especialidad_nombre ?? ""}</p>
                    {med.descripcion && <p className="text-slate-400 text-xs mt-0.5">{med.descripcion}</p>}
                  </div>
                </Link>
              ))}
            </div>
          )}
        </div>
      )}

      {/* STEP 3: Fecha */}
      {especialidadId && medicoId && !fecha && (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
          <div className="flex items-center gap-3 mb-6">
            <Link href={`/agendar?especialidad_id=${especialidadId}`} className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
              <i className="fas fa-arrow-left text-xs"></i>
            </Link>
            <h2 className="font-bold text-slate-800 text-lg flex items-center gap-2">
              <span className="step-badge">3</span> Seleccioná la Fecha
            </h2>
          </div>

          {props.medicoSel && (
            <div className="flex items-center gap-3 p-4 bg-sky-50 border border-sky-100 rounded-xl mb-6">
              <div className="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0" style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}>
                {props.medicoSel.nombre.charAt(0).toUpperCase()}
              </div>
              <div>
                <p className="font-semibold text-slate-800 text-sm">Dr. {props.medicoSel.nombre} {props.medicoSel.apellido}</p>
                <p className="text-sky-600 text-xs">{props.medicoSel.especialidad_nombre ?? ""}</p>
              </div>
            </div>
          )}

          <label className="block text-sm font-semibold text-slate-700 mb-2">¿Qué día preferís?</label>
          <input
            type="date"
            min={props.minDate}
            max={props.maxDate}
            defaultValue=""
            onChange={(e) => e.target.value && nav({ fecha: e.target.value, hora: "" })}
            className="input-field w-full md:w-64"
          />
          <p className="text-slate-400 text-xs mt-2"><i className="fas fa-info-circle mr-1"></i>Podés reservar hasta 60 días de anticipación.</p>
        </div>
      )}

      {/* STEP 4: Horario */}
      {especialidadId && medicoId && fecha && !hora && (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
          <div className="flex items-center gap-3 mb-6">
            <Link href={`/agendar?especialidad_id=${especialidadId}&medico_id=${medicoId}`} className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 transition">
              <i className="fas fa-arrow-left text-xs"></i>
            </Link>
            <h2 className="font-bold text-slate-800 text-lg flex items-center gap-2">
              <span className="step-badge">4</span> Seleccioná el Horario
            </h2>
          </div>

          {props.medicoSel && (
            <p className="text-slate-500 text-sm mb-5">
              Dr. {props.medicoSel.nombre} {props.medicoSel.apellido} —{" "}
              <span className="font-semibold text-slate-700">{fecha.split("-").reverse().join("/")}</span>
            </p>
          )}

          {medicoNoDisponible ? (
            <div className="bg-red-50 border border-red-200 rounded-xl p-5 text-center text-red-700">
              <p className="font-semibold">{props.slots[0]?.mensaje ?? "El médico no está disponible."}</p>
            </div>
          ) : props.slots.length === 0 ? (
            <div className="bg-orange-50 border border-orange-200 rounded-xl p-6 text-center text-orange-700">
              <i className="fas fa-calendar-times text-3xl mb-2 block opacity-50"></i>
              <p className="font-semibold">No hay horarios disponibles para este día.</p>
              <p className="text-sm mt-1">El médico no atiende este día. Probá con otra fecha.</p>
            </div>
          ) : (
            <div className="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-2 sm:gap-3">
              {props.slots.map((slot, i) => (
                <button
                  key={i}
                  type="button"
                  disabled={!slot.disponible}
                  onClick={() => slot.hora && nav({ hora: slot.hora })}
                  className={`py-2 sm:py-3 px-1 sm:px-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-semibold text-center transition ${slot.disponible ? "slot-available" : "slot-taken"}`}
                >
                  {slot.hora}
                </button>
              ))}
            </div>
          )}
        </div>
      )}

      {/* STEP 5: Datos */}
      {especialidadId && medicoId && fecha && hora && (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 fade-up">
          {state?.error && (
            <div className="mb-5 flex items-center gap-3 p-3.5 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-800">
              <i className="fas fa-exclamation-circle text-red-500"></i>
              {state.error}
            </div>
          )}
          <form action={formAction} className="space-y-5">
            <input type="hidden" name="especialidad_id" value={especialidadId} />
            <input type="hidden" name="medico_id" value={medicoId} />
            <input type="hidden" name="fecha" value={fecha} />
            <input type="hidden" name="hora" value={hora} />

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1.5">Nombre Completo *</label>
                <input type="text" name="nombre_paciente" required className="input-field" placeholder="Ej: Juan Pérez" />
              </div>
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono *</label>
                <input type="tel" name="telefono" required className="input-field" placeholder="0981 000 000" />
              </div>
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Correo Electrónico</label>
              <input type="email" name="email" className="input-field" placeholder="correo@ejemplo.com" />
            </div>

            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Motivo de la Consulta *</label>
              <textarea name="motivo" required rows={3} className="input-field resize-none" placeholder="Describí brevemente el motivo..."></textarea>
            </div>

            <div className="flex flex-col md:flex-row gap-3 pt-2">
              <button
                type="submit"
                disabled={pending}
                className="flex-1 py-4 rounded-xl text-white font-bold text-base shadow-lg hover:shadow-xl transition-all disabled:opacity-60"
                style={{ background: "linear-gradient(135deg,#0284c7,#0e7490)" }}
              >
                {pending ? <span className="spinner" /> : "Confirmar Cita"}
              </button>
              <Link href="/" className="flex-1 py-4 rounded-xl bg-slate-100 text-slate-600 font-bold text-base hover:bg-slate-200 transition text-center flex items-center justify-center">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      )}
    </div>
  );
}
