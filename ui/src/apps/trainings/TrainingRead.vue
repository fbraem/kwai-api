<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <div class="uk-grid">
        <div class="uk-width-1-1@s uk-width-5-6@m">
          <h1 class="uk-h1">{{ $t('training.events.title') }}</h1>
        </div>
        <div class="uk-width-1-1@s uk-width-1-6@m">
          <div class="uk-flex uk-flex-right">
            <div>
              <router-link class="uk-icon-button uk-link-reset"
                :to="{ name: 'trainings.browse' }">
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
      <div v-if="training" class="uk-flex-center" uk-grid>
        <div class="uk-width-1-2@s">
          <div class="uk-card uk-card-default">
            <div class="uk-card-header uk-padding-remove">
              <div class="uk-grid-collapse" uk-grid>
                <div class="uk-width-1-2@m uk-light uk-text-center uk-padding" style="background-color:rgb(198, 28, 24)">
                  <span style="font-size:3em; line-height:1em; text-transform:lowercase;">{{ dayName }}</span><br />
                  <span style="font-size:8em; font-weight:900; line-height:1em;">{{ day }}</span><br />
                  <span style="font-size:3em; line-height:1em; text-transform:lowercase;">{{ month }}</span>
                </div>
                <div class="uk-width-1-2@m uk-text-center uk-padding">
                  <span style="font-size:4em; line-height:1em; text-transform:lowercase;">{{ training.formattedStartTime}}</span><br />
                  <span style="font-size:4em; line-height:1em; text-transform:lowercase;">-</span><br />
                  <span style="font-size:4em; text-transform:lowercase;">{{ training.formattedEndTime}}</span><br />
                </div>
              </div>
            </div>
            <div class="uk-card-body">
              <h3 class="uk-card-title">Training &bull; {{ training.content.title }}</h3>
              <p>
                {{ training.content.summary }}
              </p>
              <p v-if="training.event.cancelled" class="uk-alert-danger" uk-alert>
                {{ $t('cancelled' )}}
              </p>
            </div>
            <div v-if="training.coaches" class="uk-card-footer">
              <strong>Coaches:</strong>
              <ul class="uk-list uk-list-bullet">
                <li v-for="(coach, index) in training.coaches" :key="index">{{ coach.name }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import moment from 'moment';
import messages from './lang';

import trainingStore from '@/stores/training';
import registerModule from '@/stores/mixin';

import PageHeader from '@/site/components/PageHeader.vue';

export default {
  components: {
    PageHeader
  },
  i18n: messages,
  mixins: [
    registerModule(
      {
        training: trainingStore
      }
    ),
  ],
  computed: {
    training() {
      return this.$store.getters['training/training'](
        this.$route.params.id
      );
    },
    day() {
      return this.training.event.start_date.date();
    },
    dayName() {
      return this.training.event.start_date.format('dddd');
    },
    month() {
      return this.training.event.start_date.format('MMMM');
    },
    error() {
      return this.$store.state.training.error;
    },
    updateAllowed() {
      return this.training
        && this.$training.isAllowed('update', this.training);
    },
    updateLink() {
      return {
        name: 'trainings.update',
        params: {
          id: this.training.id }
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
      this.$store.dispatch('training/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
