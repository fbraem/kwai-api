<template>
  <!-- eslint-disable max-len -->
  <div>
    <div v-if="$wait.is('teamtypes.read')" class="uk-flex-center" uk-grid>
      <div class="uk-text-center">
        <i class ="fas fa-spinner fa-2x fa-spin"></i>
      </div>
    </div>
    <div v-if="notAllowed" class="uk-alert-danger" uk-alert>
      {{ $t('not_allowed') }}
    </div>
    <div v-if="notFound" class="uk-alert-danger" uk-alert>
      {{ $t('not_found') }}
    </div>
    <div v-if="$wait.is('teams.read')" class="uk-flex-center" uk-grid>
      <div class="uk-text-center">
        <i class ="fas fa-spinner fa-2x fa-spin"></i>
      </div>
    </div>
    <div v-if="teamtype" class="uk-child-width-1-1" uk-grid>
      <div>
        <table class="uk-table uk-table-striped">
          <tr>
            <th>{{ $t('name') }}</th>
            <td>{{ teamtype.name }}</td>
          </tr>
          <tr>
            <th>{{ $t('form.team_type.start_age.label') }}</th>
            <td>{{ teamtype.start_age }}</td>
          </tr>
          <tr>
            <th>{{ $t('form.team_type.end_age.label') }}</th>
            <td>{{ teamtype.end_age }}</td>
          </tr>
          <tr>
            <th>{{ $t('form.team_type.gender.label') }}</th>
            <td>{{ gender }}</td>
          </tr>
          <tr>
            <th>{{ $t('active') }}</th>
            <td>
              <i class="fas fa-check" v-if="teamtype.active"></i>
              <i class="fas fa-times uk-text-danger" v-else name="times"></i>
            </td>
          </tr>
          <tr>
            <th>{{ $t('competition_label') }}<br /></th>
            <td>
              <i class="fas fa-check" v-if="teamtype.competition"></i>
              <i class="fas fa-times uk-text-danger" v-else></i>
            </td>
          </tr>
          <tr>
            <th>{{ $t('form.team_type.remark.label') }}</th>
            <td>{{ teamtype.remark }}</td>
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
    teamtype() {
      return this.$store.getters['teamType/type'](this.$route.params.id);
    },
    gender() {
      var gender = this.teamtype.gender;
      if (gender === 0) {
        return this.$t('no_restriction');
      } else if (gender === 1) {
        return this.$t('male');
      } else {
        return this.$t('female');
      }
    },
    error() {
      return this.$store.state.teamType.error;
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
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData(params) {
      this.$store.dispatch('teamType/read', { id: params.id })
        .catch((error) => {
          console.log(error);
        });
    }
  }
};
</script>
