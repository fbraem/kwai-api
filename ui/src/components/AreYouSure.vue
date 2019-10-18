<template>
  <Modal
    :show="show"
    @close="close"
  >
    <template slot="header">
      <slot name="title"></slot>
    </template>
    <template slot="default">
      <slot></slot>
    </template>
    <template slot="footer">
      <div class="flex justify-between mt-6">
        <button
          class="default-button"
          type="button"
          @click.prevent.stop="close"
        >
          <i class="fas fa-ban"></i>&nbsp;&nbsp;{{ no }}
        </button>
        <button
          class="red-button"
          type="button"
          @click="ok"
        >
          <i class="fas fa-trash"></i>&nbsp;&nbsp;{{ yes }}
        </button>
      </div>
    </template>
  </Modal>
</template>

<script>
import Modal from './Modal';

export default {
  props: [
    'no', 'yes', 'show',
  ],
  components: {
    Modal
  },
  methods: {
    ok() {
      this.$emit('sure');
    },
    close() {
      this.$emit('close');
    }
  }
};
</script>
