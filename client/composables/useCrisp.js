export const useCrisp = () => {

  function push(args) {
    if (process.client) {
      window.$crisp.push(args)
    }
  }

  function openChat() {
    push(['do', 'chat:open'])
  }

  function showChat() {
    push(['do', 'chat:show'])
  }

  function hideChat() {
    push(['do', 'chat:hide'])
  }

  function closeChat() {
    push(['do', 'chat:close'])
  }

  function openAndShowChat(message = null) {
    showChat()
    openChat()
    if (message) sendTextMessage(message)
  }

  function openHelpdesk(){
    push(['do', 'helpdesk:search'])
  }
  function openHelpdeskArticle(articleSlug, lang = 'en') {
    push(['do', 'helpdesk:article:open', [lang, articleSlug]])
  }

  function sendTextMessage(message) {
    push(['do', 'message:send', ['text',message]])
  }

  return {
    push,
    openChat,
    showChat,
    hideChat,
    closeChat,
    openAndShowChat,
    openHelpdesk,
    openHelpdeskArticle,
    sendTextMessage
  }
}
