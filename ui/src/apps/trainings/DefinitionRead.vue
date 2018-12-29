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
              <th>{{ $t('training.definitions.form.location.label') }}</th>
              <td>{{ definition.location }}</td>
            </tr>
            <tr>
              <th>{{ $t('training.definitions.form.description.label') }}</th>
              <td>{{ definition.description }}</td>
            </tr>
            <tr>
              <th>{{ $t('training.definitions.form.weekday.label') }}</th>
              <td>{{ definition.weekdayText }}</td>
            </tr>
            <tr>
              <th>{{ $t('training.definitions.time') }}</th>
              <td>
                {{ definition.start_time.format('HH:mm') }} - {{ definition.end_time.format('HH:mm') }}
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
            <tr>
              <th>{{ $t('training.definitions.form.remark.label') }}</th>
              <td>{{ definition.remark }}</td>
            </tr>
          </table>
        </div>
        <TrainingGeneratorForm :definition="definition"></TrainingGeneratorForm>
      </div>
    </section>
  </div>
</template>

<script>
import messages from './lang';

import trainingStore from '@/stores/training';
import definitionStore from '@/stores/training/definitions';
import registerModule from '@/stores/mixin';

import PageHeader from '@/site/components/PageHeader.vue';
import TrainingGeneratorForm from './TrainingGeneratorForm.vue';

export default {
  components: {
    PageHeader, TrainingGeneratorForm
  },
  i18n: messages,
  mixins: [
    registerModule(
      {
        training: trainingStore,
        definition: definitionStore,
      }
    ),
  ],
  computed: {
    definition() {
      return this.$store.getters['training/definition/definition'](
        this.$route.params.id
      );
    },
    error() {
      return this.$store.state.training.definition.error;
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
