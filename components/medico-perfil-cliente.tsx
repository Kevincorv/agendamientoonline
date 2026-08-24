"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useFormStatus } from "react-dom";

type Win = typeof window & { showToast?: (m: string, t?: "success" | "error" | "info") => void };

export function MedicoPerfilCliente({
  medico,
}: {
  medico: { id: number; nombre: string; apellido: string; email: string; telefono: string; matricula: string; descripcion: string; disponible: number; especialidad_nombre: string | null };
}) {
  const router = useRouter();
  return (
    <>
      <div className="mb-5 fade-up">
        <h1 className="text-xl font-extrabold text-slate-800">Mi Perfil</h1>
        <p className="text-sm text-slate-400">Datos profesionales y disponibilidad</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="card p-5 fade-up">
          <div className="flex flex-col items-center text-center">
            <div
              className="w-20 h-20 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mb-3"
              style={{ background: "linear-gradient(135deg,#7c3aed,#a78bfa)" }}
            >
              {medico.nombre.charAt(0).toUpperCase()}{medico.apellido.charAt(0).toUpperCase()}
            </div>
            <p className="font-extrabold text-slate-800">Dr. {medico.nombre} {medico.apellido}</p>
            <p className="text-xs text-sky-600 font-semibold">{medico.especialidad_nombre ?? "—"}</p>
            <p className="text-xs text-slate-400 mt-1">{medico.matricula || "Sin matrícula"}</p>
            <div className="mt-4 flex flex-col gap-2 w-full">
              <button
                onClick={async () => {
                  const r = await fetch("/api/medico/disponibilidad/toggle", { method: "POST" });
                  const res = await r.json();
                  (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
                  if (res.success) router.refresh();
                }}
                className={`btn w-full ${medico.disponible ? "btn-success" : "btn-secondary"}`}
              >
                <i className={`fas ${medico.disponible ? "fa-toggle-on" : "fa-toggle-off"}`}></i>
                {medico.disponible ? "Disponible" : "No disponible"}
              </button>
            </div>
          </div>
        </div>

        <div className="md:col-span-2 card p-5 fade-up">
          <h2 className="text-sm font-bold text-slate-700 mb-4">Datos profesionales</h2>
          <form
            action={async (fd) => {
              const r = await fetch("/api/medico/perfil", { method: "POST", body: fd });
              const res = await r.json();
              (window as Win).showToast?.(res.message ?? "OK", res.success ? "success" : "error");
              if (res.success) router.refresh();
            }}
            className="space-y-4"
          >
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Nombre</label>
                <input type="text" name="nombre" defaultValue={medico.nombre} className="input-field" required />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Apellido</label>
                <input type="text" name="apellido" defaultValue={medico.apellido} className="input-field" required />
              </div>
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Email</label>
              <input type="email" name="email" defaultValue={medico.email} className="input-field" />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Teléfono</label>
                <input type="tel" name="telefono" defaultValue={medico.telefono} className="input-field" />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1">Matrícula</label>
                <input type="text" name="matricula" defaultValue={medico.matricula} className="input-field" />
              </div>
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1">Descripción</label>
              <textarea name="descripcion" rows={3} defaultValue={medico.descripcion} className="input-field resize-none" />
            </div>
            <Submit />
          </form>
        </div>
      </div>
    </>
  );
}

function Submit() {
  const { pending } = useFormStatus();
  return (
    <button type="submit" className="btn btn-primary" disabled={pending}>
      {pending ? <span className="spinner" /> : <><i className="fas fa-save"></i> Guardar cambios</>}
    </button>
  );
}
