import type { Config } from "tailwindcss";

const config: Config = {
  content: ["./index.html", "./src/**/*.{ts,tsx}"],
  darkMode: "class",
  theme: {
    container: {
      center: true,
      padding: { DEFAULT: "1.5rem", md: "2rem", lg: "3rem" },
      screens: { "2xl": "1440px" },
    },
    extend: {
      colors: {
        ink: {
          DEFAULT: "#070707",
          900: "#0B0B0B",
          800: "#111111",
          700: "#171717",
          600: "#1F1F1F",
          500: "#2A2A2A",
        },
        chrome: {
          DEFAULT: "#E8E8E8",
          50: "#F5F5F5",
          100: "#E8E8E8",
          200: "#C8C8C8",
          300: "#A8A8A8",
          400: "#8A8A8A",
          500: "#6E6E6E",
          600: "#525252",
          700: "#3A3A3A",
        },
        rust: {
          DEFAULT: "#FF3B1F",
          50: "#FFE8E2",
          100: "#FFCBBE",
          200: "#FFA28A",
          300: "#FF7A5C",
          400: "#FF512E",
          500: "#FF3B1F",
          600: "#D72A11",
          700: "#A21F0C",
        },
        hairline: "rgb(var(--hairline) / <alpha-value>)",
        "hairline-strong": "rgb(var(--hairline-strong) / <alpha-value>)",
        surface: {
          DEFAULT: "rgb(var(--surface) / <alpha-value>)",
          raised: "rgb(var(--surface-raised) / <alpha-value>)",
          sunken: "rgb(var(--surface-sunken) / <alpha-value>)",
        },
        fg: {
          DEFAULT: "rgb(var(--fg) / <alpha-value>)",
          muted: "rgb(var(--fg-muted) / <alpha-value>)",
          subtle: "rgb(var(--fg-subtle) / <alpha-value>)",
          faint: "rgb(var(--fg-faint) / <alpha-value>)",
        },
        accent: {
          DEFAULT: "rgb(var(--accent) / <alpha-value>)",
          soft: "rgb(var(--accent-soft) / <alpha-value>)",
        },
      },
      fontFamily: {
        display: ["'Space Grotesk'", "system-ui", "sans-serif"],
        body: ["Inter", "system-ui", "sans-serif"],
        mono: ["'JetBrains Mono'", "ui-monospace", "monospace"],
      },
      fontSize: {
        "display-xl": ["clamp(4rem, 14vw, 14rem)", { lineHeight: "0.88", letterSpacing: "-0.04em" }],
        "display-lg": ["clamp(3rem, 9vw, 8rem)", { lineHeight: "0.92", letterSpacing: "-0.035em" }],
        "display-md": ["clamp(2.25rem, 6vw, 5rem)", { lineHeight: "0.96", letterSpacing: "-0.025em" }],
        "display-sm": ["clamp(1.75rem, 4vw, 3rem)", { lineHeight: "1.02", letterSpacing: "-0.02em" }],
        eyebrow: ["0.6875rem", { lineHeight: "1", letterSpacing: "0.32em" }],
      },
      letterSpacing: {
        tightest: "-0.045em",
        widest2: "0.4em",
      },
      backgroundImage: {
        "chrome-text":
          "linear-gradient(180deg, #FFFFFF 0%, #C8C8C8 38%, #6E6E6E 62%, #FFFFFF 100%)",
        "chrome-thin": "linear-gradient(180deg, #F5F5F5 0%, #8A8A8A 100%)",
        "ink-fade":
          "linear-gradient(180deg, rgba(7,7,7,0) 0%, rgba(7,7,7,0.55) 50%, #070707 100%)",
      },
      boxShadow: {
        pane:
          "inset 0 0 0 1px rgb(var(--hairline) / 1), 0 30px 80px -40px rgb(var(--shadow) / 0.9)",
        "pane-hover":
          "inset 0 0 0 1px rgb(var(--hairline-strong) / 1), 0 40px 90px -40px rgb(var(--shadow) / 1)",
        glow:
          "0 0 0 1px rgb(var(--accent) / 0.4), 0 0 40px -10px rgb(var(--accent) / 0.45)",
        "glow-soft":
          "0 0 0 1px rgb(var(--accent) / 0.18), 0 0 28px -12px rgb(var(--accent) / 0.28)",
      },
      transitionTimingFunction: {
        "out-expo": "cubic-bezier(0.16, 1, 0.3, 1)",
        "in-out-expo": "cubic-bezier(0.87, 0, 0.13, 1)",
      },
      keyframes: {
        scanDrift: {
          "0%": { transform: "translateY(0)" },
          "100%": { transform: "translateY(4px)" },
        },
        flicker: {
          "0%, 91%, 100%": { opacity: "1" },
          "92%, 94%": { opacity: "0.55" },
          "93%": { opacity: "0.85" },
        },
        caret: {
          "0%, 49%": { opacity: "1" },
          "50%, 100%": { opacity: "0" },
        },
        fadeIn: {
          "0%": { opacity: "0" },
          "100%": { opacity: "1" },
        },
        fadeUp: {
          "0%": { opacity: "0", transform: "translateY(14px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        glowPulse: {
          "0%, 100%": { boxShadow: "0 0 0 1px rgb(var(--accent) / 0.18), 0 0 28px -12px rgb(var(--accent) / 0.20)" },
          "50%": { boxShadow: "0 0 0 1px rgb(var(--accent) / 0.32), 0 0 38px -10px rgb(var(--accent) / 0.40)" },
        },
      },
      animation: {
        "scan-drift": "scanDrift 6s ease-in-out infinite alternate",
        flicker: "flicker 9s steps(1) infinite",
        caret: "caret 1s steps(1) infinite",
        "fade-in": "fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both",
        "fade-up": "fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both",
        "glow-pulse": "glowPulse 3.6s ease-in-out infinite",
      },
    },
  },
  plugins: [],
};

export default config;
