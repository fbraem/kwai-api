<template>
  <div>
    <div
      v-if="notAllowed"
      class="uk-alert-danger"
      uk-alert
    >
        {{ $t('not_allowed') }}
    </div>
    <div
      v-if="notFound"
      class="uk-alert-danger"
      uk-alert
    >
        {{ $t('training.coaches.not_found') }}
    </div>
    <Spinner v-if="$wait.is('training.coaches.read')" />
    <div
      v-if="coach"
      uk-grid
    >
      <div class="uk-width-1-1">
        <table class="uk-table uk-table-striped">
          <tr>
            <th>
              {{ $t('name') }}
            </th>
            <td>
              {{ coach.name }}
            </td>
          </tr>
          <tr>
            <th>
              {{ $t('training.coaches.form.diploma.label') }}
            </th>
            <td>
              {{ coach.diploma }}
            </td>
          </tr>
          <tr>
            <th>
              {{ $t('training.coaches.form.description.label') }}
            </th>
            <td>
              {{ coach.description }}
            </td>
          </tr>
          <tr>
              <th>
                {{ $t('training.coaches.form.active.label') }}
              </th>
              <td>
                  <i
                    v-if="coach.active"
                    class="fas fa-check">
                  </i>
                  <i
                    v-else
                    class="fas fa-times uk-text-danger"
                  >
                  </i>
              </td>
          </tr>
          <tr>
            <th>
              {{ $t('training.coaches.form.remark.label') }}
            </th>
            <td>
              {{ coach.remark }}
            </td>
          </tr>
        </table>
      </div>
      <div class="uk-width-1-1">
        <router-view name="coach_information" />
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Spinner from '@/components/Spinner';

export default {
  components: {
    Spinner
  },
  i18n: messages,
  computed: {
    coach() {
      return this.$store.getters['training/coach/coach'](
        this.$route.params.id
      );
    },
    error() {
      return this.$store.state.training.coach.error;
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
    }
  }
};
</script>
