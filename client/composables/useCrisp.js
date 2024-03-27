export const useCrisp = () => {

  let crisp = import.meta.client ? window.Crisp : null

  function openChat() {
    if (!crisp) return
    showChat()
    crisp.chat.open()
  }

  function showChat() {
    if (!crisp) return
    crisp.chat.show()
  }

  function hideChat() {
    if (!crisp) return
    crisp.chat.hide()
  }

  function closeChat() {
    if (!crisp) return
    crisp.chat.close()
  }

  function openAndShowChat(message = null) {
    if (!crisp) return
    openChat()
    if (message) sendTextMessage(message)
  }

  function openHelpdesk() {
    if (!crisp) return
    openChat()
    crisp.chat.setHelpdeskView()
  }

  function openHelpdeskArticle(articleSlug, locale = 'en') {
    if (!crisp) return
    crisp.chat.openHelpdeskArticle(locale, articleSlug);
  }

  function sendTextMessage(message) {
    if (!crisp) return
    crisp.message.send('text', message)
  }

  function setUser(user) {
    if (!crisp) return
    crisp.user.setEmail(user.email);
    crisp.user.setNickname(user.name);
    crisp.session.setData({
      user_id: user.id,
      'pro-subscription': user?.is_subscribed ?? false,
      'stripe-id': user?.stripe_id ?? '',
      'subscription': user?.has_enterprise_subscription ? 'enterprise' : 'pro'
    });

    if (user?.is_subscribed ?? false) {
      setSegments(['subscribed', user?.has_enterprise_subscription ? 'enterprise' : 'pro'])
    }
  }

  function pushEvent(event, data = {}) {
    if (!crisp) return
    crisp.session.pushEvent(event, data)
  }

  function setSegments(segments, overwrite = false) {
    if (!crisp) return
    crisp.session.setSegments(segments, overwrite)
  }

  return {
    crisp,
    openChat,
    showChat,
    hideChat,
    closeChat,
    openAndShowChat,
    openHelpdesk,
    openHelpdeskArticle,
    sendTextMessage,
    pushEvent,
    setUser
  }
}
