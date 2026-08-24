import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";

export const dynamic = "force-dynamic";

export default async function PacienteRootPage() {
  const session = await auth();
  if (!session?.user) redirect("/paciente/login");
  if (session.user.rol !== "paciente") redirect("/admin/dashboard");
  redirect("/paciente/dashboard");
}
