<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="member"
    class="container mx-auto p-4"
  >
    <h1>Teams</h1>
    <p>
      Dit is een overzicht van de teams waarvan
      <strong>{{ member.person.name }}</strong>
      lid is.
    </p>
    <pre>
      {{ member }}
    </pre>
  </div>
</template>

<script>
export default {
  computed: {
    member() {
      return this.$store.getters['member/member'](this.$route.params.id);
    }
  }
};
</script>
