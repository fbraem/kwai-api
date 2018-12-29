<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <div class="uk-grid">
        <div class="uk-width-1-1@s uk-width-5-6@m">
          <h1 class="uk-h1">{{ $t('training.events.title') }}</h1>
          <h3 class="uk-h3 uk-margin-remove">
            {{ $t('training.events.generate') }}
          </h3>
        </div>
        <div class="uk-width-1-1@s uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <div>
            </div>
            <div class="uk-margin-small-left">
            </div>
          </div>
        </div>
      </div>
    </PageHeader>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <div class="uk-child-width-1-1" uk-grid>
        <div v-if="count ===0">
          <div class="uk-alert uk-alert-warning">
            {{ $t('training.events.no_generated_data') }}
          </div>
        </div>
        <div v-else class="uk-child-width-1-1" uk-grid>
          <div>
            <table class="uk-table uk-table-divider uk-table-striped uk-table-middle uk-table-small">
              <tr>
                <th></th>
                <th>{{ $t('training.events.day') }}</th>
                <th>{{ $t('training.events.date') }}</th>
                <th>{{ $t('training.events.time') }}</th>
                <th class="uk-table-expand">{{ $t('training.events.coaches') }}</th>
              </tr>
              <tr v-for="(event, index) in events" :key="index"
                :class="{ 'uk-text-muted': event.disabled }"
                :style="{'text-decoration': event.disabled ? 'line-through' : 'none'}">
                <td>
                  <a v-if="event.disabled" class="uk-icon-button" @click="event.disabled = false">
                    <i class="fas fa-calendar-plus"></i>
                  </a>
                  <a v-else class="uk-icon-button" @click="event.disabled = true">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
                <td>{{ event.start_date.format('dddd') }}</td>
                <td>{{ event.start_date.format('L') }}</td>
                <td>{{ event.start_time.format('HH:mm') }} - {{ event.end_time.format('HH:mm') }}</td>
                <td></td>
              </tr>
            </table>
          </div>
          <div uk-grid>
            <div class="uk-width-expand">
            </div>
            <div class="uk-width-auto">
              <button class="uk-button uk-button-primary" @click="submit">
                <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import trainingStore from '@/stores/training';
import eventStore from '@/stores/training/events';
import registerModule from '@/stores/mixin';

import PageHeader from '@/site/components/PageHeader.vue';

import messages from './lang';

export default {
  components: {
    PageHeader
  },
  i18n: messages,
  mixins: [
    registerModule(
      {
        training: trainingStore,
        event: eventStore
      }
    ),
  ],
  computed: {
    events() {
      return this.$store.state.training.event.events;
    },
    count() {
      if (this.events) {
        return this.events.length;
      }
      return 0;
    }
  },
  methods: {
    submit() {
      this.$store.dispatch('training/event/createAll');
    }
  }
};
</script>
