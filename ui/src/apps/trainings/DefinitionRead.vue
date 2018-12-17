<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <div class="uk-grid">
        <div class="uk-width-1-1@s uk-width-5-6@m">
          <h1 class="uk-h1">{{ $t('training.definitions.title') }}</h1>
          <h3 v-if="definition" class="uk-h3 uk-margin-remove">
            {{ definition.name }}
          </h3>
        </div>
        <div class="uk-width-1-1@s uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <div>
              <router-link class="uk-icon-button uk-link-reset"
                :to="{ name: 'trainings.definitions.browse' }">
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
            {{ $t('training.definitions.not_found') }}
        </div>
        <div v-if="$wait.is('training.definitions.read')" class="uk-flex-center" uk-grid>
          <div class="uk-text-center">
            <i class="fas fa-spinner fa-2x fa-spin"></i>
          </div>
        </div>
        <div v-if="definition" class="uk-child-width-1-1" uk-grid>
          <div>
            <table class="uk-table uk-table-striped">
              <tr>
                <th>{{ $t('training.definitions.form.name.label') }}</th>
                <td>{{ definition.name }}</td>
              </tr>
              <tr>
                <th>{{ $t('training.definitions.form.description.label') }}</th>
                <td>{{ definition.description }}</td>
              </tr>
              <tr>
                <th>{{ $t('training.definitions.form.weekday.label') }}</th>
                <td>{{ weekday }}</td>
              </tr>
              <tr>
                <th>{{ $t('training.definitions.time') }}</th>
                <td>
                  {{ definition.localStartTime }} - {{ definition.localEndTime }}
                </td>
              </tr>
              <tr>
                <th>{{ $t('training.definitions.form.team.label') }}</th>
                <td v-if="definition.team">{{ definition.team.name }}</td>
                <td v-else><i class="fas fa-minus"></i></td>
              </tr>
              <tr>
                <th>{{ $t('training.definitions.form.season.label') }}</th>
                <td v-if="definition.season">{{ definition.season.name }}</td>
                <td v-else><i class="fas fa-minus"></i></td>
              </tr>
            </table>
          </div>
        </div>
      </section>
    </div>
</template>

<script>
import moment from 'moment';

import messages from './lang';

import trainingDefinitionStore from '@/stores/training/definitions';
import registerModule from '@/stores/mixin';

import PageHeader from '@/site/components/PageHeader.vue';

export default {
  components: {
    PageHeader,
  },
  i18n: messages,
  mixins: [
    registerModule([
      {
        namespace: 'trainingDefinitionModule',
        store: trainingDefinitionStore
      }]
    ),
  ],
  data() {
    return {
    };
  },
  computed: {
    definition() {
      return this.$store.getters['trainingDefinitionModule/definition'](
        this.$route.params.id
      );
    },
    error() {
      return this.$store.getters['trainingDefinitionModule/error'];
    },
    updateAllowed() {
      return this.definition
        && this.$training_definition.isAllowed('update', this.definition);
    },
    updateLink() {
      return {
        name: 'trainings.definitions.update',
        params: {
          id: this.definition.id }
      };
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    },
    weekday() {
      return moment.weekdays(true)[this.definition.weekday - 1];
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
      this.$store.dispatch('trainingDefinitionModule/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
