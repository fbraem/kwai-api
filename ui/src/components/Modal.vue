<template>
  <transition name="kwai-modal-fade">
    <div
      class="kwai-modal"
      @click.prevent.stop="click"
    >
      <div class="kwai-modal-container" role="dialog">
        <header>
          <slot name="header"></slot>
        </header>
        <span
          class="kwai-modal-close"
          @click.prevent.stop="close"
        >
          &times;
        </span>
        <div class="kwai-modal-content">
          <slot></slot>
        </div>
        <footer>
          <slot name="footer"></slot>
        </footer>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  methods: {
    close() {
      this.$emit('close');
    },
    click(e) {
      if (e.target.className === 'kwai-modal') this.$emit('close');
    }
  }
};
</script>
