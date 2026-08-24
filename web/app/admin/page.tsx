import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";

export const dynamic = "force-dynamic";

export default async function AdminRootPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol === "medico") redirect("/medico/dashboard");
  if (session.user.rol === "paciente") redirect("/paciente/dashboard");
  redirect("/admin/dashboard");
}
