import { serialize } from "object-to-formdata"
import Errors from "./Errors"
import cloneDeep from "clone-deep"
import { opnFetch } from "~/composables/useOpnApi.js"
function hasFiles(data) {
  return (
    data instanceof File ||
    data instanceof Blob ||
    data instanceof FileList ||
    (typeof data === "object" &&
      data !== null &&
      Object.values(data).find((value) => hasFiles(value)) !== undefined)
  )
}

class Form {
  constructor(data = {}) {
    this.originalData = {}
    this.busy = false
    this.successful = false
    this.recentlySuccessful = false
    this.recentlySuccessfulTimeoutId = undefined
    this.errors = new Errors()
    this.update(data)
  }

  static errorMessage = "Something went wrong. Please try again."
  static recentlySuccessfulTimeout = 2000
  static ignore = [
    "busy",
    "successful",
    "errors",
    "originalData",
    "recentlySuccessful",
    "recentlySuccessfulTimeoutId",
  ]

  static make(augment) {
    return new this(augment)
  }

  update(data) {
    this.originalData = Object.assign({}, this.originalData, cloneDeep(data))
    Object.assign(this, data)
  }

  fill(data = {}) {
    this.keys().forEach((key) => {
      this[key] = data[key]
    })
  }

  data() {
    return this.keys().reduce(
      (data, key) => ({ ...data, [key]: this[key] }),
      {},
    )
  }

  keys() {
    return Object.keys(this).filter((key) => !Form.ignore.includes(key))
  }

  startProcessing() {
    this.errors.clear()
    this.busy = true
    this.successful = false
    this.recentlySuccessful = false
    clearTimeout(this.recentlySuccessfulTimeoutId)
  }

  finishProcessing() {
    this.busy = false
    this.successful = true
    this.recentlySuccessful = true
    this.recentlySuccessfulTimeoutId = setTimeout(() => {
      this.recentlySuccessful = false
    }, Form.recentlySuccessfulTimeout)
  }

  clear() {
    this.errors.clear()
    this.successful = false
    this.recentlySuccessful = false
    clearTimeout(this.recentlySuccessfulTimeoutId)
  }

  resetAndFill(data = {}) {
    // Clear form state
    this.clear()
    
    // Reset and update form data using the existing update method
    this.originalData = {}
    this.update(data)
    
    return this
  }

  reset() {
    Object.keys(this)
      .filter((key) => !Form.ignore.includes(key))
      .forEach((key) => {
        this[key] = cloneDeep(this.originalData[key])
      })
  }

  get(url, config = {}) {
    return this.submit("get", url, config)
  }

  post(url, config = {}) {
    return this.submit("post", url, config)
  }

  patch(url, config = {}) {
    return this.submit("patch", url, config)
  }

  put(url, config = {}) {
    return this.submit("put", url, config)
  }

  delete(url, config = {}) {
    return this.submit("delete", url, config)
  }

  submit(method, url, config = {}) {
    this.startProcessing()

    config = {
      body: {},
      params: {},
      url: url,
      method: method,
      ...config,
    }

    if (method.toLowerCase() === "get") {
      config.params = { ...this.data(), ...config.params }
    } else {
      config.body = { ...this.data(), ...config.data, ...config.body }

      if (hasFiles(config.data) && !config.transformRequest) {
        config.transformRequest = [(data) => serialize(data)]
      }
    }
    return new Promise((resolve, reject) => {
      opnFetch(config.url, config)
        .then((data) => {
          this.finishProcessing()
          resolve(data)
        })
        .catch((error) => {
          this.handleErrors(error)
          reject(error)
        })
    })
  }

  validate(method, url, config = {}, fieldsToValidate = {}) {
    this.startProcessing()
    const headers = {
      'Precognition': true,
      'Precognition-Validate-Only': Array.from(fieldsToValidate).join(),
      ...config.headers
    }
    config = {
      body: {},
      params: {},
      url: url,
      method: method,
      headers,
      ...config,
    }
    if (method.toLowerCase() === "get") {
      config.params = { ...this.data(), ...config.params }
    } else {
      config.body = { ...this.data(), ...config.data }

      if (hasFiles(config.data) && !config.transformRequest) {
        config.transformRequest = [(data) => serialize(data)]
      }
    }
    return new Promise((resolve, reject) => {
      opnFetch(config.url, config)
        .then((data) => {
          this.finishProcessing()
          resolve(data)
        })
        .catch((error) => {
          this.handleErrors(error)
          reject(error)
        })
    })
  }

  handleErrors(error) {
    this.busy = false

    if (error) {
      this.errors.set(this.extractErrors(error.data))
    }
  }

  extractErrors(data) {
    if (!data || typeof data !== "object") {
      return { error: Form.errorMessage }
    }

    if (data.errors) {
      return { ...data.errors }
    }

    if (data.message) {
      return { error: data.message }
    }

    return { ...data }
  }

  onKeydown(event) {
    const target = event.target

    if (target.name) {
      this.errors.clear(target.name)
    }
  }
}

export default Form
