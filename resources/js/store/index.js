import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

// Load store modules dynamically.
const requireContext = import.meta.glob('./modules/**/*.js', {eager: true})
const modules = Object.keys(requireContext)

  .map(file =>
    [file.replace(/(^.\/)|(\.js$)/g, '').replace('modules/',''), requireContext[file]]
  )
  .reduce((modules, [name, module]) => {
    if (module.namespaced === undefined) {
      module = {...module, namespaced: true}
    }

    return { ...modules, [name]: module }
  }, {})
export default new Vuex.Store({
  modules
})
