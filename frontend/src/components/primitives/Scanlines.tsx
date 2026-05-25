export function Scanlines() {
  return (
    <div
      aria-hidden
      className="pointer-events-none fixed inset-0 z-[55] mix-blend-overlay animate-scan-drift"
      style={{
        opacity: "var(--scan-opacity)" as unknown as number,
        backgroundImage:
          "repeating-linear-gradient(to bottom, transparent 0px, transparent 2px, rgb(var(--fg) / 0.4) 3px)",
      }}
    />
  );
}
