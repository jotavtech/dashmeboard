import { Outlet, useLocation } from "react-router-dom";
import { AnimatePresence, motion, useReducedMotion } from "framer-motion";
import { Sidebar } from "@/components/chrome/Sidebar";
import { Header } from "@/components/chrome/Header";
import { Grain } from "@/components/primitives/Grain";
import { Scanlines } from "@/components/primitives/Scanlines";

export default function RootLayout() {
  const location = useLocation();
  const reduce = useReducedMotion();

  return (
    <div className="relative flex min-h-screen bg-surface text-fg">
      <Grain />
      <Scanlines />

      <Sidebar />

      <div className="flex min-w-0 flex-1 flex-col">
        <Header />
        <main className="relative flex-1 overflow-x-hidden">
          <AnimatePresence mode="wait" initial={false}>
            <motion.div
              key={location.pathname}
              initial={reduce ? false : { opacity: 0, y: 16, filter: "blur(6px)" }}
              animate={{ opacity: 1, y: 0, filter: "blur(0px)" }}
              exit={reduce ? { opacity: 0 } : { opacity: 0, y: -8, filter: "blur(4px)" }}
              transition={{ duration: 0.42, ease: [0.16, 1, 0.3, 1] }}
              className="will-change-[opacity,transform,filter]"
            >
              <Outlet />
            </motion.div>
          </AnimatePresence>
        </main>
      </div>
    </div>
  );
}
