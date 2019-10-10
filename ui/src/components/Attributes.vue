<template>
  <dl>
    <div
      v-for="(attribute, name) in attributes"
      :key="name"
      class="flex flex-wrap border-b last:border-b-0 p-3"
    >
      <dt class="font-bold w-full sm:w-1/3">
        <slot name="label" :attribute="{ name, attribute }">
          {{ attribute.label }}
        </slot>
      </dt>
      <dd class="w-full sm:w-2/3">
        <slot name="value" :attribute="{ name, attribute }">
          {{ attribute.value }}
        </slot>
      </dd>
    </div>
  </dl>
</template>

<!-- A sample of the scopes:
<template v-slot:label="{attribute}">
  {{ attribute.label }}
</template>
<template v-slot:value="{attribute}">
  {{ attribute.value }}
</template>
-->

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
