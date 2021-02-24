<template>
  <div id="header-sticky" :style="{height:height+'px', max_height: height+'px'}" class="mb-20 el-row">
    <div
      :class="(className || 'header-sticky') + (active ? '-active': '')"
      :style="{top:stickyTop+'px',zIndex:zIndex,position:position,width:width, background: background, paddingBottom:paddingBottom+'px'}"
    >
      <el-row>
        <el-col :span="24" class="header-sticky-in" style="max-height: 36px;">

          <div
            v-if="$slots.append"
            :class="classNameAppend"
            style="display: inline; float: left; vertical-align: middle;"
          >
            <slot name="append" />
          </div>
          <div v-else id="header-sticky-title" :class="classNameAppend" style="display: inline; float: left; vertical-align: middle;">
            <el-button
              icon="el-icon-back"
              style="margin-right: 10px;"
              circle
              title="Вернуться назад"
              @click="backPage()"
            />
            <span :style="styleTitle" class="page-h1">

              {{ title }}

              <slot v-if="$slots.title" name="title" />

            </span>
          </div>

          <div v-if="$slots.prepend" id="header-sticky-actions" :class="classNamePrepend" style="display: inline; float: right; vertical-align: middle;">
            <div v-if="hamburger">

              <el-popover
                placement="bottom-end"
                width="200"
                trigger="click"
              >
                <div class="hamburger-custom-header-sticky">
                  <slot name="prepend" />
                </div>
                <el-button slot="reference" icon="el-icon-s-unfold" type="text" style="font-size: 29px; color: #606266;" />
              </el-popover>

            </div>
            <div v-else>
              <slot name="prepend" />
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HeaderSticky',
  props: {
    stickyTop: {
      type: Number,
      default: 84
    },
    zIndex: {
      type: Number,
      default: 99
    },
    className: {
      type: String,
      default: ''
    },
    classNameAppend: {
      type: String,
      default: ''
    },
    classNamePrepend: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    styleTitle: {
      type: String,
      default: 'vertical-align: middle;'
    },
    background: {
      type: String,
      default: '#fff'
    },
    backgroundAppMain: {
      type: String,
      default: () => ''
    },
    paddingBottom: {
      type: Number,
      default: () => 10
    }
  },
  data() {
    return {
      active: false,
      position: '',
      width: undefined,
      height: undefined,
      isSticky: false,
      hamburger: false,
      currentInterval: null,
      actions_size: 0
    }
  },
  mounted() {
    document.getElementsByClassName('app-main')[0].style.backgroundColor = this.backgroundAppMain
    this.height = this.$el.getBoundingClientRect().height
    window.addEventListener('scroll', this.handleScroll)
    window.onresize = (e) => {
      this.hamburgerSetter()
    }
    window.addEventListener('resize', this.handleReize)
    this.hamburgerSetter()
  },
  activated() {
    this.handleScroll()
  },
  destroyed() {
    window.removeEventListener('scroll', this.handleScroll)
    window.removeEventListener('resize', this.handleReize)
  },
  methods: {
    hamburgerSetter() {
      if (document.getElementById('header-sticky') && document.getElementById('header-sticky-actions') && document.getElementById('header-sticky-title')) {
        if (this.currentInterval) {
          clearTimeout(this.currentInterval)
          this.currentInterval = null
        }

        this.currentInterval = setTimeout(() => {
          var actions_size = document.getElementById('header-sticky-actions').clientWidth
          if (actions_size > this.actions_size) {
            this.actions_size = actions_size
          }

          var container_size = document.getElementById('header-sticky').clientWidth
          this.width = container_size + 'px'
          var title_size = document.getElementById('header-sticky-title').clientWidth
          var hamburger = (container_size - title_size - this.actions_size) < 10
          this.hamburger = hamburger
        }, 500)
      }
    },
    sticky() {
      if (this.active) {
        return
      }
      this.position = 'fixed'
      this.active = true
      this.width = this.width > 36 ? this.width + 'px' : '36px'
      this.isSticky = true
    },
    reset() {
      if (!this.active) {
        return
      }
      this.position = ''
      this.width = 'auto'
      this.active = false
      this.isSticky = false
    },
    handleScroll() {
      this.width = this.$el.getBoundingClientRect().width
      const offsetTop = this.$el.getBoundingClientRect().top
      if (offsetTop < this.stickyTop) {
        this.sticky()
        return
      }
      this.reset()
    },
    handleReize() {
      if (this.isSticky) {
        this.width = this.$el.getBoundingClientRect().width + 'px'
      }
    },
    backPage() {
      this.$store.dispatch('tagsView/delView', this.$router.currentRoute)
      this.$router.go(-1)
    }
  }
}
</script>
<style lang="scss">
  .header-sticky {
    padding-top: 10px;
    padding-bottom: 10px;
    background-color: #fff;
  }
  .header-sticky-active {
    padding-top: 10px;
    padding-bottom: 10px;
    background-color: #fff;
    border-bottom: 1px solid #ebebeb;
  }
  .hamburger-custom-header-sticky {
    button {
      width: 100%!important;
      margin: 5px 0 5px 0!important;
    }
  }
</style>
