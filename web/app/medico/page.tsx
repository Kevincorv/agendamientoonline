import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";

export const dynamic = "force-dynamic";

export default async function MedicoRootPage() {
  const session = await auth();
  if (!session?.user) redirect("/admin/login");
  if (session.user.rol !== "medico") redirect("/admin/dashboard");
  redirect("/medico/dashboard");
}
