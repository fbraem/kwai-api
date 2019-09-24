<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <div
      v-if="notAllowed"
      class="kwai-alert kwai-theme-danger"
    >
        {{ $t('not_allowed') }}
    </div>
    <div
      v-if="notFound"
      class="kwai-alert kwai-alert-danger"
    >
        {{ $t('training.definitions.not_found') }}
    </div>
    <Spinner v-if="$wait.is('training.definitions.read')" />
    <div
      v-if="definition"
      style="grid-column: span 2"
    >
      <table class="kwai-table kwai-table-striped">
        <tr>
          <th>
            {{ $t('training.definitions.form.name.label') }}
          </th>
          <td>
            {{ definition.name }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.form.location.label') }}
          </th>
          <td>
            {{ definition.location }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.form.description.label') }}
          </th>
          <td>
            {{ definition.description }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.form.weekday.label') }}
          </th>
          <td>
            {{ definition.weekdayText }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.time') }}
          </th>
          <td>
            {{ definition.start_time.format('HH:mm') }} - {{ definition.end_time.format('HH:mm') }}
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.form.team.label') }}
          </th>
          <td v-if="definition.team">
            {{ definition.team.name }}
          </td>
          <td v-else>
            <i class="fas fa-minus"></i>
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.form.season.label') }}
          </th>
          <td v-if="definition.season">
            {{ definition.season.name }}
          </td>
          <td v-else>
            <i class="fas fa-minus"></i>
          </td>
        </tr>
        <tr>
          <th>
            {{ $t('training.definitions.form.remark.label') }}
          </th>
          <td>
            {{ definition.remark }}
          </td>
        </tr>
      </table>
      <TrainingGeneratorForm :definition="definition" />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import TrainingGeneratorForm from './TrainingGeneratorForm.vue';
import Spinner from '@/components/Spinner';

export default {
  components: {
    Spinner,
    TrainingGeneratorForm
  },
  i18n: messages,
  computed: {
    definition() {
      return this.$store.getters['training/definition/definition'](
        this.$route.params.id
      );
    },
    error() {
      return this.$store.state.training.definition.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.id);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.id);
    next();
  },
  methods: {
    fetchData(id) {
      this.$store.dispatch('training/definition/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
