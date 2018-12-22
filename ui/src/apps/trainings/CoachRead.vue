<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <div class="uk-grid">
        <div class="uk-width-1-1@s uk-width-5-6@m">
          <h1 class="uk-h1">{{ $t('training.coaches.title') }}</h1>
          <h3 v-if="coach" class="uk-h3 uk-margin-remove">
            {{ coach.name }}
          </h3>
        </div>
        <div class="uk-width-1-1@s uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <div>
              <router-link class="uk-icon-button uk-link-reset"
                :to="{ name: 'trainings.coaches.browse' }">
                <i class="fas fa-list"></i>
              </router-link>
              </div>
              <div class="uk-margin-small-left">
                <router-link v-if="updateAllowed"
                  class="uk-icon-button uk-link-reset" :to="updateLink">
                  <i class="fas fa-edit"></i>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </PageHeader>
      <section class="uk-section uk-section-small uk-container uk-container-expand">
        <div v-if="notAllowed" class="uk-alert-danger" uk-alert>
            {{ $t('not_allowed') }}
        </div>
        <div v-if="notFound" class="uk-alert-danger" uk-alert>
            {{ $t('training.coaches.not_found') }}
        </div>
        <div v-if="$wait.is('training.coaches.read')" class="uk-flex-center" uk-grid>
          <div class="uk-text-center">
            <i class="fas fa-spinner fa-2x fa-spin"></i>
          </div>
        </div>
        <div v-if="coach" class="uk-child-width-1-1" uk-grid>
          <div>
            <table class="uk-table uk-table-striped">
              <tr>
                <th>{{ $t('name') }}</th>
                <td>{{ coach.name }}</td>
              </tr>
              <tr>
                <th>{{ $t('training.coaches.form.diploma.label') }}</th>
                <td>{{ coach.diploma }}</td>
              </tr>
              <tr>
                <th>{{ $t('training.coaches.form.description.label') }}</th>
                <td>{{ coach.description }}</td>
              </tr>
              <tr>
                  <th>{{ $t('training.coaches.form.active.label') }}</th>
                  <td>
                      <i class="fas fa-check" v-if="coach.active"></i>
                      <i class="fas fa-times uk-text-danger" v-else name="times"></i>
                  </td>
              </tr>
            </table>
          </div>
        </div>
      </section>
    </div>
</template>

<script>
import messages from './lang';

import trainingStore from '@/stores/training';
import coachStore from '@/stores/training/coaches';
import registerModule from '@/stores/mixin';

import PageHeader from '@/site/components/PageHeader.vue';

export default {
  components: {
    PageHeader,
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
  data() {
    return {
    };
  },
  computed: {
    coach() {
      return this.$store.getters['training/coach/coach'](
        this.$route.params.id
      );
    },
    error() {
      return this.$store.state.training.coach.error;
    },
    updateAllowed() {
      return this.coach
        && this.$training_coach.isAllowed('update', this.coach);
    },
    updateLink() {
      return {
        name: 'trainings.coaches.update',
        params: {
          id: this.coach.id }
      };
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
      this.$store.dispatch('training/coach/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
