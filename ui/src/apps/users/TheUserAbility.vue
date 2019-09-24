<template>
  <tbody>
    <td
      class="kwai-table-expand kwai-table-middle"
      style="vertical-align: middle;"
    >
      {{ ability.name }}
      <div v-if="!hide">
        <h4>Rules</h4>
        <table class="kwai-table kwai-table-small">
          <tr>
            <th>name</th>
            <th>subject</th>
            <th>action</th>
          </tr>
          <tr v-for="rule in ability.rules" :key="rule.id">
            <td>{{ rule.name }}</td>
            <td>{{ rule.subject.name }}</td>
            <td>{{ rule.action.name }}</td>
          </tr>
        </table>
      </div>
    </td>
    <td>
      <router-link
        :to="{ name: 'users.abilities.update', params: { id: ability.id } }"
        class="kwai-icon-button"
      >
        <i class="fas fa-edit"></i>
      </router-link>
      <a
        v-if="add"
        class="kwai-icon-button"
        @click="emitAdd(ability)"
      >
        <i class="fas fa-plus"></i>
      </a>
      <a
        v-if="remove"
        class="kwai-icon-button"
        @click="emitRemove(ability)"
      >
        <i class="fas fa-trash"></i>
      </a>
      <a v-if="hide"
        @click="show"
        class="kwai-icon-button">
        <i class="fas fa-caret-right"></i>
      </a>
      <a v-else
        @click="show"
        class="kwai-icon-button">
        <i class="fas fa-caret-down"></i>
      </a>
    </td>
  </tbody>
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
