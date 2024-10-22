// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: {
    enabled: true,
  },

  modules: ["@nuxt/ui"],

  ssr: true,
  runtimeConfig: {
    // Server-only vars (used during SSR)
    apiBaseServer: 'http://127.0.0.1:80',
    public: {
      // Client-side vars (available in browser)
      apiBaseClient: process.env.NODE_ENV === 'production'
        ? '/api' // In production, use relative path
        : 'http://localhost:8888' // In development, use exposed port
    }
  }
})