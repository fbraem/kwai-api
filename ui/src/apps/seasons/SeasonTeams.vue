<template>
  <div v-if="season">
    <h1>Teams</h1>
    <table
      v-if="season.teams && season.teams.length > 0"
      class="table"
    >
      <tbody>
        <tr v-for="team in season.teams" :key="team.id">
          <td>{{ team.name }}</td>
        </tr>
      </tbody>
    </table>
    <Alert v-else>
      Er zijn nog geen teams gekoppeld aan dit seizoen!
    </Alert>
  </div>
</template>

<script>
import Alert from '@/components/Alert';

export default {
  components: {
    Alert
  },
  computed: {
    season() {
      return this.$store.state.season.active;
    },
  }
};
</script>
