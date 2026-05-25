import type { ElementType, HTMLAttributes, ReactNode } from "react";
import { cn } from "@/lib/cn";
import { useTheme } from "@/contexts/ThemeContext";

type ChromeTextProps = {
  as?: ElementType;
  variant?: "bright" | "muted";
  children: ReactNode;
  className?: string;
} & HTMLAttributes<HTMLElement>;

const gradients = {
  dark: {
    bright:
      "linear-gradient(180deg, #FFFFFF 0%, #C8C8C8 38%, #6E6E6E 62%, #FFFFFF 100%)",
    muted: "linear-gradient(180deg, #F5F5F5 0%, #8A8A8A 100%)",
  },
  light: {
    bright:
      "linear-gradient(180deg, #0F0F0F 0%, #303030 38%, #6E6E6E 62%, #0F0F0F 100%)",
    muted: "linear-gradient(180deg, #2A2A2A 0%, #8A8A8A 100%)",
  },
} as const;

export function ChromeText({
  as: Tag = "span",
  variant = "bright",
  children,
  className,
  ...rest
}: ChromeTextProps) {
  const { theme } = useTheme();
  const gradient = gradients[theme][variant];

  return (
    <Tag
      className={cn("bg-clip-text text-transparent transition-[background-image] duration-500", className)}
      style={{
        WebkitBackgroundClip: "text",
        WebkitTextFillColor: "transparent",
        backgroundImage: gradient,
        textShadow:
          theme === "dark"
            ? "0 1px 0 rgba(255,255,255,0.04)"
            : "0 1px 0 rgba(0,0,0,0.04)",
      }}
      {...rest}
    >
      {children}
    </Tag>
  );
}
