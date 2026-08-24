import "../../styles/app.css";
import { PublicHeader } from "@/components/public-header";
import { PublicFooter } from "@/components/public-footer";

export default function PublicLayout({ children }: { children: React.ReactNode }) {
  return (
    <>
      <PublicHeader />
      <div id="toastContainer" className="toast-container"></div>
      <main className="flex-1">{children}</main>
      <PublicFooter />
    </>
  );
}
