<template>
  <div
    class="alert"
    :style="style"
  >
    <i
      v-if="icon"
      :class="icon_class"
      style="margin-right: 1rem;"
    >
    </i>
    <div style="vertical-align: middle">
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
.alert {
  display: flex;
  padding: 1rem;
  align-items: center;
}
</style>

<script>

const icons = {
  danger: 'fas fa-exclamation-circle',
  info: 'fas fa-info-circle',
  warning: 'fas fa-bell',
  success: 'fas fa-thumbs-up'
};

export default {
  props: {
    type: {
      type: String,
      default: 'info'
    },
    icon: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    style() {
      return {
        'background-color': 'var(--kwai-color-' + this.type + '-light)',
        color: 'var(--kwai-color-' + this.type + '-dark)',
        border: '2px solid var(--kwai-color-' + this.type + '-dark)',
      };
    },
    icon_class() {
      return icons[this.type] || 'fas fa-exclamation-circle';
    }
  }
};
</script>
