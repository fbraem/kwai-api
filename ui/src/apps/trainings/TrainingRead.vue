<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="training"
    class="uk-flex-center"
    uk-grid
  >
    <div class="uk-width-1-2@s">
      <div class="uk-card uk-card-default">
        <div class="uk-card-header uk-padding-remove">
          <div
            class="uk-grid-collapse"
            uk-grid
          >
            <div
              class="uk-width-1-2@m uk-light uk-text-center uk-padding"
              style="background-color:rgb(198, 28, 24)"
            >
              <div style="font-size:2em; line-height:1em; text-transform:lowercase;">
                {{ dayName }}
              </div>
              <div style="font-size:8em; font-weight:900; line-height:1em;">
                {{ day }}
              </div>
              <div style="font-size:2em; line-height:1em; text-transform:lowercase;">
                {{ month }}
              </div>
            </div>
            <div class="uk-width-1-2@m uk-text-center uk-padding">
              <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
                {{ training.formattedStartTime}}
              </div>
              <div style="font-size:4em; line-height:1em; text-transform:lowercase;">
                -
              </div>
              <div style="font-size:4em; text-transform:lowercase;">
                {{ training.formattedEndTime}}
              </div>
              <br />
            </div>
          </div>
        </div>
        <div class="uk-card-body">
          <h3 class="uk-card-title">
            Training &bull; {{ training.content.title }}
          </h3>
          <p>
            {{ training.content.summary }}
          </p>
          <p
            v-if="training.event.cancelled"
            class="uk-alert-danger"
            uk-alert
          >
            {{ $t('cancelled' )}}
          </p>
        </div>
        <div class="uk-card-footer">
          <div uk-grid>
            <div
              v-if="training.coaches"
              class="uk-width-1-1">
              <strong>Coaches:</strong>
              <ul class="uk-list uk-list-bullet">
                <li
                  v-for="(coach, index) in training.coaches"
                  :key="index">
                  {{ coach.coach.name }}
                </li>
              </ul>
            </div>
            <div class="uk-width-1-1">
              <strong>Aanwezigheden:</strong>
              <div>
                <a class="uk-icon-button uk-link-reset">
                  <i class="fas fa-address-book"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
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
