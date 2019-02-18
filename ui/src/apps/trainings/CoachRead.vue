<template>
  <!-- eslint-disable max-len -->
  <div>
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
          <tr>
            <th>{{ $t('training.coaches.form.remark.label') }}</th>
            <td>{{ coach.remark }}</td>
          </tr>
        </table>
      </div>
      <div>
        <router-view name="coach_information"></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
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
