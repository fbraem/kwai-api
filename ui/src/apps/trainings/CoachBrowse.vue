<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <div class="uk-grid">
        <div class="uk-width-5-6">
          <h1>{{ $t('training.coaches.title') }}</h1>
        </div>
        <div class="uk-width-1-6">
          <div class="uk-flex uk-flex-right">
            <router-link v-if="$training_coach.isAllowed('create')"
              class="uk-icon-button uk-link-reset"
              :to="{ name : 'trainings.coaches.create' }">
              <i class="fas fa-plus"></i>
            </router-link>
          </div>
        </div>
      </div>
    </PageHeader>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <div v-if="$wait.is('training.coaches.browse')"
        class="uk-flex-center" uk-grid>
        <div class="uk-text-center">
          <i class="fas fa-spinner fa-2x fa-spin"></i>
        </div>
      </div>
      <div v-else class="uk-child-width-1-1" uk-grid>
        <div v-if="noData">
          <div class="uk-alert uk-alert-warning">
            {{ $t('training.coaches.no_data') }}
          </div>
        </div>
        <div v-else>
          <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
            <tr>
              <th>{{ $t('name') }}</th>
              <th>{{ $t('training.coaches.form.diploma.label') }}</th>
            </tr>
            <tr v-for="coach in coaches" :key="coach.id">
              <td>
                <router-link :to="{ name: 'trainings.coaches.read', params: { id : coach.id} }">{{ coach.member.person.name }}</router-link>
              </td>
              <td>
                {{ coach.diploma }}
              </td>
            </tr>
          </table>
        </div>
      </div>
    </section>
  </div>
</template>


<script>
import PageHeader from '@/site/components/PageHeader';

import messages from './lang';

import trainingStore from '@/stores/training';
import coachStore from '@/stores/training/coaches';
import registerModule from '@/stores/mixin';

export default {
  components: {
    PageHeader
  },
  i18n: messages,
  mixins: [
    registerModule(
      {
        training: trainingStore,
        coach: coachStore,
      }
    ),
  ],
  computed: {
    coaches() {
      return this.$store.state.training.coach.coaches;
    },
    noData() {
      return this.coaches && this.coaches.length === 0;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData();
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData();
    next();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('training/coach/browse');
    }
  }
};
</script>
