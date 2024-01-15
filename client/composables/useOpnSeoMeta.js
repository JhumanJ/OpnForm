export const useOpnSeoMeta = (meta) => {
  return useSeoMeta({
    ...meta.title ? {
      ogTitle: meta.title,
      twitterTitle: meta.title,
    } : {},
    ...meta.description ? {
      ogDescription: meta.description,
      twitterDescription: meta.description,
    } : {},
    ...meta.ogImage ? {
      twitterImage: meta.ogImage,
    } : {},
    ...meta,
  })
}
