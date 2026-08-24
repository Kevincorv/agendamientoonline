"use server";

import { revalidatePath } from "next/cache";
import { auth } from "@/lib/auth";
import {
  crearEspecialidad,
  actualizarEspecialidad,
  eliminarEspecialidad,
  obtenerEspecialidadPorId,
} from "@/lib/repos/especialidades";
import { crearMedico, listarMedicosAdmin } from "@/lib/repos/medicos";
import { crearHorario } from "@/lib/repos/horarios";
import { auditLog } from "@/lib/audit";

const HORARIO_DEFAULT = [
  { dia: 1, ini: "07:00", fin: "12:00" },
  { dia: 1, ini: "14:00", fin: "18:00" },
  { dia: 2, ini: "07:00", fin: "12:00" },
  { dia: 2, ini: "14:00", fin: "18:00" },
  { dia: 3, ini: "07:00", fin: "12:00" },
  { dia: 3, ini: "14:00", fin: "18:00" },
  { dia: 4, ini: "07:00", fin: "12:00" },
  { dia: 4, ini: "14:00", fin: "18:00" },
  { dia: 5, ini: "07:00", fin: "12:00" },
  { dia: 5, ini: "14:00", fin: "18:00" },
  { dia: 6, ini: "07:00", fin: "12:00" },
  { dia: 6, ini: "14:00", fin: "18:00" },
];

async function requireAdmin() {
  const session = await auth();
  if (!session?.user || session.user.rol === "paciente" || session.user.rol === "medico") {
    throw new Error("No autorizado");
  }
  return session;
}

export async function crearEspAction(formData: FormData) {
  const session = await requireAdmin();
  const nombre = String(formData.get("nombre") ?? "").trim();
  const descripcion = String(formData.get("descripcion") ?? "").trim() || null;
  const icono = String(formData.get("icono") ?? "fa-stethoscope");
  if (!nombre) return;

  const id = await crearEspecialidad({ nombre, descripcion, icono });

  // Auto-crear médico por defecto con horarios
  const medicoId = await crearMedico({
    nombre,
    apellido: "(por defecto)",
    email: null,
    telefono: null,
    especialidad_id: id,
    matricula: null,
    descripcion: `Médico automático de ${nombre}`,
  });

  for (const h of HORARIO_DEFAULT) {
    await crearHorario({
      medico_id: medicoId,
      dia_semana: h.dia,
      hora_inicio: h.ini,
      hora_fin: h.fin,
      duracion: 30,
      intervalo_minutos: 30,
    });
  }

  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "crear_medico_auto",
    tabla: "medicos",
    registroId: medicoId,
    descripcion: `Médico automático creado para ${nombre}`,
  });

  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "crear",
    tabla: "especialidades",
    registroId: id,
    descripcion: `Especialidad creada: ${nombre}`,
  });

  revalidatePath("/admin/especialidades");
}

export async function editarEspAction(formData: FormData) {
  const session = await requireAdmin();
  const id = Number(formData.get("id") ?? 0);
  const nombre = String(formData.get("nombre") ?? "").trim();
  const descripcion = String(formData.get("descripcion") ?? "").trim() || null;
  const icono = String(formData.get("icono") ?? "fa-stethoscope");
  const prev = await obtenerEspecialidadPorId(id);
  if (!id || !nombre) return;
  await actualizarEspecialidad(id, { nombre, descripcion, icono });
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "editar",
    tabla: "especialidades",
    registroId: id,
    descripcion: `Especialidad editada: ${nombre}`,
    datosAntes: prev ? { nombre: prev.nombre } : null,
    datosDespues: { nombre },
  });
  revalidatePath("/admin/especialidades");
}

export async function eliminarEspAction(formData: FormData) {
  const session = await requireAdmin();
  const id = Number(formData.get("id") ?? 0);
  if (!id) return;
  const esp = await obtenerEspecialidadPorId(id);
  await eliminarEspecialidad(id);
  await auditLog({
    usuarioId: session.user.id,
    usuarioNombre: `${session.user.nombre} ${session.user.apellido}`,
    accion: "eliminar",
    tabla: "especialidades",
    registroId: id,
    descripcion: `Especialidad desactivada: ${esp?.nombre ?? "#" + id}`,
  });
  revalidatePath("/admin/especialidades");
}
