import type { DefaultSession } from "next-auth";

declare module "next-auth" {
  interface Session {
    user: {
      id: number;
      nombre: string;
      apellido: string;
      rol: string;
      rolId: number | null;
    } & DefaultSession["user"];
  }

  interface User {
    id: number;
    nombre: string;
    apellido: string;
    rol: string;
    rolId: number | null;
  }
}

declare module "next-auth/jwt" {
  interface JWT {
    id: number;
    nombre: string;
    apellido: string;
    rol: string;
    rolId: number | null;
  }
}
