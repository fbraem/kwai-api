<template>
  <tbody>
    <td class="uk-width-expand">
      {{ rule_group.name }}
      <div v-if="!hide" class="uk-panel uk-background-muted uk-padding-small">
        <h4>Rules</h4>
        <table class="uk-table uk-table-small">
          <tr>
            <th>name</th>
            <th>subject</th>
            <th>action</th>
          </tr>
          <tr v-for="rule in rule_group.rules" :key="rule.id">
            <td>{{ rule.name }}</td>
            <td>{{ rule.subject.name }}</td>
            <td>{{ rule.action.name }}</td>
          </tr>
        </table>
      </div>
    </td>
    <td>
      <a v-if="hide"
        @click="show"
        class="uk-icon-button uk-link-reset">
        <i class="fas fa-caret-right"></i>
      </a>
      <a v-else
        @click="show"
        class="uk-icon-button uk-link-reset">
        <i class="fas fa-caret-down"></i>
      </a>
    </td>
  </tbody>
</template>

<script>
export default {
  props: {
    rule_group: {
      type: Object,
      required: true
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
    }
  }
};
</script>
