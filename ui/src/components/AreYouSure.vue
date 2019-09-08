<template>
  <div class="kwai-modal" @click.prevent.stop="click">
    <div class="kwai-modal-container" role="dialog">
      <header>
        <slot name="title"></slot>
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
        <div
          style="display: flex;justify-content: flex-end; margin-right: 20px;"
          class="kwai-buttons"
        >
          <button
            class="kwai-button"
            type="button"
          >
            <i class="fas fa-ban"></i>&nbsp;&nbsp;{{ no }}
          </button>
          <button
            class="kwai-button kwai-theme-danger"
            type="button"
            @click="ok"
          >
            <i class="fas fa-trash"></i>&nbsp;&nbsp;{{ yes }}
          </button>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  props: [
    'no', 'yes',
  ],
  methods: {
    ok() {
      this.$emit('sure');
    },
    close() {
      this.$emit('close');
    },
    click(e) {
      if (e.target.className === 'kwai-modal') this.$emit('close');
    }
  }
};
</script>
