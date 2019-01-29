<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <div class="uk-grid">
        <div class="uk-width-5-6">
          <h1>{{ $t('training.events.title') }}</h1>
        </div>
        <div class="uk-width-1-6">
          <div class="uk-flex uk-flex-right">
            <router-link v-if="$training_event.isAllowed('create')"
              class="uk-icon-button uk-link-reset"
              :to="{ name : 'trainings.events.create' }">
              <i class="fas fa-plus"></i>
            </router-link>
          </div>
        </div>
      </div>
    </PageHeader>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <div v-if="$wait.is('training.events.browse')"
        class="uk-flex-center" uk-grid>
        <div class="uk-text-center">
          <i class="fas fa-spinner fa-2x fa-spin"></i>
        </div>
      </div>
      <div v-else class="uk-child-width-1-1" uk-grid>
        <div v-if="noData">
          <div class="uk-alert uk-alert-warning">
            {{ $t('training.events.no_data') }}
          </div>
        </div>
        <div v-else>
          <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
            <tr>
              <th>{{ $t('name') }}</th>
              <th>{{ $t('training.events.date') }}</th>
              <th>{{ $t('training.events.form.start_time.label') }}</th>
              <th>{{ $t('training.events.form.end_time.label') }}</th>
              <th></th>
            </tr>
            <tr v-for="event in events" :key="event.id">
              <td>
                <router-link :to="{ name: 'trainings.events.read', params: { id : event.id} }">{{ event.name }}</router-link>
              </td>
              <td>
                {{ event.formattedStartDate }}
              </td>
              <td>
                {{ event.formattedStartTime }}
              </td>
              <td>
                {{ event.formattedEndTime }}
              </td>
              <td>
                <router-link class="uk-icon-button uk-link-reset" v-if="$training_event.isAllowed('update', event)" :to="{ name : 'trainings.events.update', params : { id : event.id } }">
                  <i class="fas fa-edit uk-text-muted"></i>
                </router-link>
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
import eventStore from '@/stores/training/events';
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
        event: eventStore,
      }
    ),
  ],
  computed: {
    events() {
      return this.$store.state.training.event.events;
    },
    noData() {
      return this.events && this.events.length === 0;
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
      this.$store.dispatch('training/event/browse');
    }
  }
};
</script>
