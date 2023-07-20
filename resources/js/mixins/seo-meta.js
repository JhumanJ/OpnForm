export default {
  metaInfo () {
    const title = this.metaTitle ?? 'OpnForm'
    const description = this.metaDescription ?? "Create beautiful forms for free. Unlimited fields, unlimited submissions. It's free and it takes less than 1 minute to create your first form."
    const image = this.metaImage ?? this.asset('img/social-preview.jpg')
    const metaTemplate = this.metaTemplate ?? '%s · OpnForm'

    return {
      title: title,
      titleTemplate: metaTemplate,
      meta: [
        ...(this.metaTags ?? []),
        { vmid: 'og:title', property: 'og:title', content: title },
        { vmid: 'twitter:title', property: 'twitter:title', content: title },
        { vmid: 'description', name: 'description', content: description },
        { vmid: 'og:description', property: 'og:description', content: description },
        { vmid: 'twitter:description', property: 'twitter:description', content: description },
        { vmid: 'twitter:image', property: 'twitter:image', content: image },
        { vmid: 'og:image', property: 'og:image', content: image }
      ]
    }
  }
}
