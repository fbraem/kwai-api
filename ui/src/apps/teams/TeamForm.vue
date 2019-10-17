<template>
  <div class="m-6">
    <KwaiForm
      :form="form"
      :error="error"
      :save="$t('save')"
      @submit="submit"
    >
      <KwaiField
        name="name"
        :label="$t('form.team.name.label')"
      >
        <KwaiInputText :placeholder="$t('form.team.name.placeholder')" />
      </KwaiField>
      <KwaiField
        name="season"
        :label="$t('form.team.season.label')"
      >
        <KwaiSelect :items="seasons" />
      </KwaiField>
      <p class="uk-text-meta">
        {{ $t('form.team.season.hint')}}
      </p>
      <KwaiField
        name="team_type"
        :label="$t('form.team.team_type.label')"
      >
        <KwaiSelect :items="team_types" />
      </KwaiField>
      <p class="uk-text-meta">
        {{ $t('form.team.team_type.hint')}}
      </p>
      <KwaiField
        name="remark"
        :label="$t('form.team.remark.label')"
      >
        <KwaiTextarea
          :rows="5"
          :placeholder="$t('form.team.remark.placeholder')"
        />
      </KwaiField>
    </KwaiForm>
  </div>
</template>

<script>
import Team from '@/models/Team';
import TeamType from '@/models/TeamType';
import Season from '@/models/Season';

import makeForm, { makeField, notEmpty } from '@/js/Form';
const makeTeamForm = (fields) => {
  const writeForm = (team) => {
    fields.name.value = team.name;
    fields.remark.value = team.remark;
    if (team.season) {
      fields.season.value = team.season.id;
    }
    if (team.team_type) {
      fields.team_type.value = team.team_type.id;
    }
  };
  const readForm = (team) => {
    team.name = fields.name.value;
    team.remark = fields.remark.value;
    if (fields.season.value === 0) {
      team.season = null;
    } else {
      team.season = new Season();
      team.season.id = fields.season.value;
    }
    if (fields.team_type.value === 0) {
      team.team_type = null;
    } else {
      team.team_type = new TeamType();
      team.team_type.id = fields.team_type.value;
    }
  };
  return { ...makeForm(fields), writeForm, readForm };
};

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText';
import KwaiTextarea from '@/components/forms/KwaiTextarea';
import KwaiSelect from '@/components/forms/KwaiSelect';

import messages from './lang';

export default {
  components: {
    KwaiForm, KwaiField, KwaiInputText, KwaiTextarea, KwaiSelect
  },
  i18n: messages,
  data() {
    return {
      team: new Team(),
      form: makeTeamForm({
        name: makeField({
          value: '',
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('form.team.name.required'),
            },
          ]
        }),
        season: makeField({
          value: 0
        }),
        team_type: makeField({
          value: 0
        }),
        remark: makeField({
          value: ''
        })
      })
    };
  },
  computed: {
    creating() {
      return this.team != null && this.team.id == null;
    },
    error() {
      return this.$store.state.team.error;
    },
    seasons() {
      var seasons = this.$store.getters['season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('form.team.season.empty')
      });
      return seasons;
    },
    team_types() {
      var seasons = this.$store.getters['teamType/typesAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('form.team.team_type.empty')
      });
      return seasons;
    },
  },
  async created() {
    await this.$store.dispatch('season/browse');
    await this.$store.dispatch('teamType/browse');
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    if (to.params.id) await this.fetchData(to.params);
    next();
  },
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv);
        } else if (nv.response.status === 404) {
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    async fetchData(params) {
      this.team = await this.$store.dispatch('team/read', {
        id: params.id
      });
      this.form.writeForm(this.team);
    },
    async submit() {
      this.form.clearErrors();
      this.form.readForm(this.team);
      try {
        this.team = await this.$store.dispatch('team/save', this.team);
        this.$router.push({
          name: 'teams.read',
          params: {
            id: this.team.id
          }
        });
      } catch (error) {
        console.log(error);
      };
    }
  }
};
</script>
