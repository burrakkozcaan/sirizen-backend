import * as React from "react"

import { cn } from "@/lib/utils"

function Input({ className, type, ...props }: React.ComponentProps<"input">) {
  return (
    <input
      type={type}
      data-slot="input"
      className={cn(
        "flex h-10 w-full min-w-0 rounded-[10px] border border-zinc-950/10 bg-transparent px-3 py-2 text-sm leading-5 text-foreground outline-none transition-colors",
        "placeholder:text-muted-foreground/60",
        "focus:border-zinc-950 dark:border-white/10 dark:focus:border-white/60",
        "file:text-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium",
        "disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50",
        "aria-invalid:border-destructive",
        className
      )}
      {...props}
    />
  )
}

export { Input }
