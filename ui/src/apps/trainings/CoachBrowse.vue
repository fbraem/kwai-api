<template>
  <!-- eslint-disable max-len -->
  <div>
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
            <th>{{ $t('training.coaches.form.active.label') }}</th>
            <th></th>
          </tr>
          <tr v-for="coach in coaches" :key="coach.id">
            <td>
              <router-link :to="{ name: 'trainings.coaches.read', params: { id : coach.id} }">{{ coach.member.person.name }}</router-link>
            </td>
            <td>
              {{ coach.diploma }}
            </td>
            <td>
              <i class="fas fa-check" v-if="coach.active"></i>
              <i class="fas fa-times uk-text-danger" v-else name="times"></i>
            </td>
            <td>
              <router-link class="uk-icon-button uk-link-reset" v-if="$training_coach.isAllowed('update', coach)" :to="{ name : 'trainings.coaches.update', params : { id : coach.id } }">
                <i class="fas fa-edit uk-text-muted"></i>
              </router-link>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

export default {
  i18n: messages,
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
