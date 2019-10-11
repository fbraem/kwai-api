<template>
  <dl>
    <div
      v-for="(attribute, name) in attributes"
      :key="name"
      class="flex flex-wrap border-b last:border-b-0 p-5"
    >
      <dt class="w-full sm:w-1/3 sm:pr-6">
        <slot name="label" :prop="name" :attribute="attribute">
          <div class="font-bold md:text-right">
            {{ attribute.label }}
          </div>
        </slot>
      </dt>
      <dd class="w-full sm:w-2/3">
        <slot name="value" :prop="name" :attribute="attribute">
          {{ attribute.value }}
        </slot>
      </dd>
    </div>
  </dl>
</template>

<script>
export default {
  props: {
    attributes: {
      required: true,
      type: Object
    }
  }
};
</script>
