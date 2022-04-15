<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>
<style>
textarea:focus, input:focus{
  outline: none;
}
input:-webkit-autofill {
  color: #ffffff !important;
}
</style>
