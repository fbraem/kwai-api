<template>
  <div
    class="flex items-center border-l-4 p-4"
    :class="cssClass"
    role="alert"
  >
    <h3 v-if="title">{{ title }}</h3>
    <i
      v-if="icon"
      class="mr-4"
      :class="icon_class"
    >
    </i>
    <div>
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

const css = {
  danger: {
    'bg-red-100': true,
    'border-red-500': true,
    'text-red-700': true
  },
  warning: {
    'bg-orange-100': true,
    'border-orange-500': true,
    'text-orange-700': true
  },
  info: {
    'bg-blue-100': true,
    'border-blue-500': true,
    'text-blue-700': true
  },
  success: {
    'bg-green-100': true,
    'border-green-500': true,
    'text-green-700': true
  }
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
    },
    title: {
      required: false
    }
  },
  computed: {
    cssClass() {
      return css[this.type] || css['info'];
    },
    icon_class() {
      return icons[this.type] || 'fas fa-exclamation-circle';
    }
  }
};
</script>
