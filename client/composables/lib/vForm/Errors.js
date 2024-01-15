
function arrayWrap(value) {
  return Array.isArray(value) ? value : [value];
}

export default class Errors {
  constructor() {
    this.errors = {};
  }

  set(field, messages = undefined) {
    if (typeof field === 'object') {
      this.errors = field;
    } else {
      this.set({ ...this.errors, [field]: arrayWrap(messages) });
    }
  }

  all() {
    return this.errors;
  }

  has(field) {
    return Object.prototype.hasOwnProperty.call(this.errors, field);
  }

  hasAny(...fields) {
    return fields.some(field => this.has(field));
  }

  any() {
    return Object.keys(this.errors).length > 0;
  }

  get(field) {
    if (this.has(field)) {
      return this.getAll(field)[0];
    }
  }

  getAll(field) {
    return arrayWrap(this.errors[field] || []);
  }

  only(...fields) {
    const messages = [];

    fields.forEach((field) => {
      const message = this.get(field);
      if (message) {
        messages.push(message);
      }
    });

    return messages;
  }

  flatten() {
    return Object.values(this.errors).reduce((a, b) => a.concat(b), []);
  }

  clear(field = undefined) {
    const errors = {};

    if (field) {
      Object.keys(this.errors).forEach((key) => {
        if (key !== field) {
          errors[key] = this.errors[key];
        }
      });
    }

    this.set(errors);
  }
}
