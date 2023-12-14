import config from "~/opnform.config.js";

export const useOpnFetch = (request, opts) => {
  return useFetch(request, { baseURL: config.api_url, ...opts })
}
