<template>
  <div :class="cssClass">
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
    cssClass() {
      const cls = {};
      cls[this.type + ':kwai-alert'] = true;
      return cls;
    },
    icon_class() {
      return icons[this.type] || 'fas fa-exclamation-circle';
    }
  }
};
</script>
