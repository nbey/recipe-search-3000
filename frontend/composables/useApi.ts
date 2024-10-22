export async function useApi(endpoint: string, key: string) {
  const config = useRuntimeConfig()

  // If running on server (SSR), use internal Docker URL
  const base = import.meta.server
    ? config.apiBaseServer
    : config.public.apiBaseClient

  return useFetch(`${base}${endpoint}`, {
    headers: {
      'Accept': 'application/json'
    },
    key,
  })
}