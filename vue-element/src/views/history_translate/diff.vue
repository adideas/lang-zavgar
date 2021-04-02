<template>
  <div v-if="diff">
    <div class="del">
      - {{ diff.old }} : <span class="count"> ( {{ String(diff.old || '').length }} ) - ( {{ String(diff.old || '').replace(/ /g,'').length }} ) </span>
    </div>
    <div class="html_diff" v-html="'+ ' + diff.html + ` : <count> ( ${diff.count_new_symbol_with_space} ) - ( ${diff.count_new_symbol_without_space} ) </count>`" />
    <div class="ins">
      = {{ diff.new }} :  <span class="count"> ( {{ String(diff.new || '').length }} ) - ( {{ String(diff.new || '').replace(/ /g,'').length }} ) </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Diff',
  props: {
    diff: {
      type: Object,
      default: () => {}
    }
  }
}
</script>

<style lang="scss">
.html_diff {
  padding: 4px 0;
  background: #eaeaea;
  del {
    border: 1px solid red;
    /* color: #fdd;*/
    color: red;
    background: #fdd;
    text-decoration: line-through;
    padding: 0 2px;
  }
  ins {
    border: 1px solid green;
    color: green;
    background: #dfd;
    text-decoration: none;
    padding: 0 2px;
  }
  count {
    border: 1px solid yellow;
    color: blue;
    background: #fff9dd;
    text-decoration: none;
    padding: 0 2px;
  }
}
.del {
  color: red;
  background: #fdd;
  text-decoration: none;
}
.ins {
  color: green;
  background: #dfd;
  text-decoration: none;
}
.count {
  border: 1px solid yellow;
  color: blue;
  background: #fff9dd;
  text-decoration: none;
  padding: 0 2px;
}
</style>
