<template>
  <div class="uk-form-controls">
    <label class="uk-switch">
      <input v-model="field.value"
        :id="id"
        type="checkbox"
        :checked="isChecked"
        v-bind="$attrs"
      />
      <div class="uk-switch-slider uk-switch-on-off round"></div>
    </label>
  </div>
</template>

<style scoped>
.uk-switch {
  position: relative;
  display: inline-block;
  height: 30px;
  width: 56px;
}

/* Hide default HTML checkbox */
.uk-switch input {
  display:none;
}
/* Slider */
.uk-switch-slider {
  background-color: rgba(0,0,0,0.22);
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  border-radius: 30px;
  bottom: 0;
  cursor: pointer;
  transition-property: background-color;
	transition-duration: .2s;
}
/* Switch pointer */
.uk-switch-slider:before {
  content: '';
  background-color: #fff;
  position: absolute;
  width: 26px;
  height: 26px;
  left: 2px;
  bottom: 2px;
  border-radius: 50%;
  transition-property: transform, box-shadow;
	transition-duration: .2s;
}
/* Slider active color */
input:checked + .uk-switch-slider {
  background-color: #39f !important;
}
/* Pointer active animation */
input:checked + .uk-switch-slider:before {
  transform: translateX(26px);
}
/* Square Modifier */
.uk-switch-slider.uk-switch-square, .uk-switch-slider.uk-switch-square:before {
  border-radius: 0;
}

/* Modifiers */
.uk-switch-slider.uk-switch-on-off {
  background-color: #f0506e;
}
input:checked + .uk-switch-slider.uk-switch-on-off {
  background-color: #32d296 !important;
}

/* Style Modifier */
.uk-switch-slider.uk-switch-big:before {
  transform: scale(1.2);
  box-shadow: 0 0 6px rgba(0,0,0,0.22);
}
input:checked + .uk-switch-slider.uk-switch-big:before {
  transform: translateX(26px) scale(1.2);
}

/* Inverse Modifier - affects only default */
.uk-light .uk-switch-slider:not(.uk-switch-on-off) {
  background-color: rgba(255,255,255,0.22);
}
</style>

<script>
export default {
  inject: {
    field: 'field',
    id: 'id'
  },
  computed: {
    isChecked() {
      return this.field.value;
    }
  }
};
</script>
