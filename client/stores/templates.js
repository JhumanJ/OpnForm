import axios from "axios";
import { defineStore } from "pinia";

export const templatesEndpoint = "/api/templates";
export const namespaced = true;

export const useTemplatesStore = defineStore("templates", {
  state: () => ({
    content: [],
    industries: {},
    types: {},
    allLoaded: false,
    loading: false,
  }),
  getters: {
    getBySlug: (state) => (slug) => {
      if (state.content.length === 0) return null;
      return state.content.find((item) => item.slug === slug);
    },
    getTemplateTypes: (state) => (slugs) => {
      if (state.types.length === 0) return null;
      return Object.values(state.types)
        .filter((val) => slugs.includes(val.slug))
        .map((item) => {
          return item.name;
        });
    },
    getTemplateIndustries: (state) => (slugs) => {
      if (state.industries.length === 0) return null;
      return Object.values(state.industries)
        .filter((val) => slugs.includes(val.slug))
        .map((item) => {
          return item.name;
        });
    },
  },
  actions: {
    set(items) {
      this.content = items;
      this.allLoaded = true;
    },
    append(items) {
      const ids = items.map((item) => {
        return item.id;
      });
      this.content = this.content.filter((val) => !ids.includes(val.id));
      this.content = this.content.concat(items);
    },
    addOrUpdate(item) {
      this.content = this.content.filter((val) => val.id !== item.id);
      this.content.push(item);
    },
    remove(item) {
      this.content = this.content.filter((val) => val.id !== item.id);
    },
    startLoading() {
      this.loading = true;
    },
    stopLoading() {
      this.loading = false;
    },
    setAllLoaded(val) {
      this.allLoaded = val;
    },
    resetState() {
      this.set([]);
      this.stopLoading();
    },
    loadTypesAndIndustries() {
      if (Object.keys(this.industries).length === 0) {
        import("@/data/forms/templates/industries.json").then((module) => {
          this.industries = module.default;
        });
      }
      if (Object.keys(this.types).length === 0) {
        import("@/data/forms/templates/types.json").then((module) => {
          this.types = module.default;
        });
      }
    },
    loadTemplate(slug) {
      this.startLoading();
      this.loadTypesAndIndustries();

      if (this.getBySlug(slug)) {
        this.stopLoading();
        return Promise.resolve();
      }

      return axios
        .get(templatesEndpoint + "/" + slug)
        .then((response) => {
          this.addOrUpdate(response.data);
          this.stopLoading();
        })
        .catch((error) => {
          this.stopLoading();
        });
    },
    loadAll(options = null) {
      this.startLoading();
      this.loadTypesAndIndustries();

      // Prepare with options
      let queryStr = "";
      if (options !== null) {
        for (const [key, value] of Object.entries(options)) {
          queryStr +=
            "&" + encodeURIComponent(key) + "=" + encodeURIComponent(value);
        }
        queryStr = queryStr.slice(1);
      }
      return axios
        .get(queryStr ? templatesEndpoint + "?" + queryStr : templatesEndpoint)
        .then((response) => {
          if (options !== null) {
            this.set(response.data);
            this.setAllLoaded(false);
          } else {
            this.append(response.data);
            this.setAllLoaded(true);
          }
          this.stopLoading();
        })
        .catch((error) => {
          this.stopLoading();
        });
    },
    loadIfEmpty() {
      if (!this.allLoaded) {
        return this.loadAll();
      }
      this.stopLoading();
      return Promise.resolve();
    },
  },
});
