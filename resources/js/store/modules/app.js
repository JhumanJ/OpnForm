export const state = {
  layout: 'default'
}

export const mutations = {
  setLayout (state, layout) {
    state.layout = layout ?? 'default'
  }
}
