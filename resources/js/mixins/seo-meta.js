export default {
  metaInfo () {
    const title = this.metaTitle ?? 'e-Form'
    const description = this.metaDescription ?? "Tạo các biểu mẫu/khảo sát đơn giản, đẹp; hoàn toàn miễn phí và không giới hạn."
    const image = this.metaImage ?? this.asset('img/social-preview.jpg')
    const metaTemplate = this.metaTemplate ?? '%s · e-Form'

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
