export {}

declare global {
  namespace NodeJS {
    interface ProcessEnv {
      NEXT_PUBLIC_SUPABASE_URL?: string
      NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY?: string
      [key: string]: string | undefined
    }
  }

  declare var process: {
    env: NodeJS.ProcessEnv
  }

  namespace JSX {
    interface IntrinsicElements {
      [elemName: string]: any
    }
  }
}
