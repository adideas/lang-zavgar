/**
 * Augment the typings of Vue.js
 */

import Vue from "vue";

declare module "vue/types/vue" {
  interface Vue {
    $query: Function;
  }
}

declare module "vue/types/options" {
  interface ComponentOptions<V extends Vue> {
    query?(query): void;
  }
}
