<template>
  <!-- eslint-disable max-len -->
  <div>
    <Spinner v-if="$wait.is('training.coaches.browse')" />
    <div
      v-else
      uk-grid
    >
      <div
        v-if="noData"
        class="uk-width-1-1"
      >
        <div class="uk-alert-warning" uk-alert>
          {{ $t('training.coaches.no_data') }}
        </div>
      </div>
      <div
        v-else
        class="uk-width-1-1"
       >
        <table class="uk-table uk-table-small uk-table-divider uk-table-middle">
          <tr>
            <th>
              {{ $t('name') }}
            </th>
            <th>
              {{ $t('training.coaches.form.diploma.label') }}
            </th>
            <th>
              {{ $t('training.coaches.form.active.label') }}
            </th>
            <th></th>
          </tr>
          <tr
            v-for="coach in coaches"
            :key="coach.id"
          >
            <td>
              <router-link :to="{ name: 'trainings.coaches.read', params: { id : coach.id} }">
                {{ coach.member.person.name }}
              </router-link>
            </td>
            <td>
              {{ coach.diploma }}
            </td>
            <td>
              <i
                v-if="coach.active"
                class="fas fa-check"
              >
              </i>
              <i
                v-else
                class="fas fa-times uk-text-danger"
              >
              </i>
            </td>
            <td>
              <router-link
                v-if="$can('update', coach)"
                class="uk-icon-button uk-link-reset"
                :to="{ name : 'trainings.coaches.update', params : { id : coach.id } }"
              >
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
import Spinner from '@/components/Spinner';
import messages from './lang';

export default {
  components: {
    Spinner
  },
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
