import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { AdminLoginForm } from "@/components/admin-login-form";

export const dynamic = "force-dynamic";

export default async function AdminLoginPage() {
  const session = await auth();
  if (session?.user) {
    if (session.user.rol === "medico") redirect("/medico/dashboard");
    if (session.user.rol === "paciente") redirect("/paciente/dashboard");
    redirect("/admin/dashboard");
  }
  return <AdminLoginForm />;
}
