// Tipos TS del dominio (mapean las tablas MySQL/TiDB)

export interface Rol {
  id: number;
  nombre: string;
  descripcion: string | null;
}

export interface Permiso {
  id: number;
  nombre: string;
  modulo: string;
  accion: string;
}

export interface Usuario {
  id: number;
  nombre: string;
  apellido: string;
  email: string;
  password: string;
  rol_id: number | null;
  rol?: string | null; // join roles.nombre
  activo: number;
  creado_en: string;
  login_attempts: number;
  locked_until: string | null;
  last_login: string | null;
  last_ip: string | null;
  two_factor_secret: string | null;
  two_factor_enabled: number;
}

export interface Especialidad {
  id: number;
  nombre: string;
  icono: string;
  descripcion: string | null;
  activo: number;
  creado_en: string;
}

export interface Medico {
  id: number;
  nombre: string;
  apellido: string;
  email: string | null;
  telefono: string | null;
  especialidad_id: number;
  especialidad_nombre?: string | null;
  matricula: string | null;
  descripcion: string | null;
  disponible: number;
  activo: number;
  usuario_id: number | null;
  creado_en: string;
}

export interface Horario {
  id: number;
  medico_id: number;
  dia_semana: number; // 0=Domingo ... 6=Sábado
  hora_inicio: string;
  hora_fin: string;
  duracion: number;
  activo: number;
  intervalo_minutos: number;
}

export interface EstadoCita {
  id: number;
  nombre: string;
  color: string;
}

export interface Cita {
  id: number;
  nombre_paciente: string;
  telefono: string | null;
  email: string | null;
  motivo: string | null;
  token_cancelacion: string | null;
  notas_medico: string | null;
  fecha: string;
  hora: string;
  medico_id: number;
  especialidad_id: number | null;
  estado_id: number;
  creado_en: string;
  // joins comunes
  medico_nombre?: string | null;
  especialidad_nombre?: string | null;
  estado_nombre?: string | null;
  estado_color?: string | null;
}

export interface Feriado {
  id: number;
  fecha: string;
  motivo: string;
  activo: number;
  created_at: string;
}

export interface BloqueoMedico {
  id: number;
  medico_id: number;
  fecha: string;
  motivo: string | null;
  created_at: string;
}

export interface Notificacion {
  id: number;
  usuario_id: number | null;
  titulo: string;
  mensaje: string | null;
  tipo: string;
  leido: number;
  created_at: string;
}

export interface Auditoria {
  id: number;
  usuario_id: number | null;
  usuario_nombre: string | null;
  accion: string;
  tabla: string | null;
  registro_id: number | null;
  descripcion: string | null;
  datos_antes: string | null;
  datos_despues: string | null;
  ip: string | null;
  user_agent: string | null;
  created_at: string;
}

export interface Sesion {
  id: number;
  usuario_id: number;
  session_token: string;
  ip: string | null;
  user_agent: string | null;
  created_at: string;
  last_activity: string;
  activa: number;
}
