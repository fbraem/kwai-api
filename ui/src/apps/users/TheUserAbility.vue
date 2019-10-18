<template>
  <div class="flex flex-col">
    <div class="flex items-center">
      <h3 class="mb-0">{{ ability.name }}</h3>
      <div class="ml-auto">
        <router-link
          :to="{ name: 'users.abilities.update', params: { id: ability.id } }"
          class="icon-button text-gray-700 hover:bg-gray-300"
        >
          <i class="fas fa-edit"></i>
        </router-link>
        <a
          v-if="add"
          class="icon-button text-gray-700 hover:bg-gray-300"
          @click="emitAdd(ability)"
        >
          <i class="fas fa-plus"></i>
        </a>
        <a
          v-if="remove"
          class="icon-button text-red-700 hover:bg-gray-300"
          @click="emitRemove(ability)"
        >
          <i class="fas fa-trash"></i>
        </a>
        <a v-if="hide"
          @click="show"
          class="icon-button text-gray-700 hover:bg-gray-300">
          <i class="fas fa-caret-right"></i>
        </a>
        <a v-else
          @click="show"
          class="icon-button text-gray-700 hover:bg-gray-300">
          <i class="fas fa-caret-down"></i>
        </a>
      </div>
    </div>
    <div
      v-if="!hide"
      class="p-3"
    >
      <table class="w-full border-collapse text-left">
        <tr class="bg-gray-500 border-b border-gray-200">
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            name
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            subject
          </th>
          <th class="py-2 px-3 font-bold uppercase text-sm text-white">
            action
          </th>
        </tr>
        <tr
          v-for="rule in ability.rules"
          :key="rule.id"
          class="odd:bg-gray-200 border-b border-gray-400"
        >
          <td class="py-2 px-3 text-gray-700">
            {{ rule.name }}
          </td>
          <td class="py-2 px-3 text-gray-700">
            {{ rule.subject.name }}
          </td>
          <td class="py-2 px-3 text-gray-700">
            {{ rule.action.name }}
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    ability: {
      type: Object,
      required: true
    },
    add: {
      type: Boolean,
      default: false
    },
    remove: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      hide: true
    };
  },
  methods: {
    show() {
      this.hide = !this.hide;
    },
    emitAdd(ability) {
      this.$emit('add', ability);
    },
    emitRemove(ability) {
      this.$emit('remove', ability);
    }
  }
};
</script>
