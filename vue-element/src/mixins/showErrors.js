export default {
  data() {
    return {
      errors: {}
    }
  },
  methods: {
    showErrors(el) {
      if (this.errors[el] && this.errors[el].length > 0) {
        return this.errors[el][0]
      } else {
        return ''
      }
    }
  }
}
