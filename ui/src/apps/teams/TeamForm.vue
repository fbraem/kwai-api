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
        name="category"
        :label="$t('form.team.category.label')"
      >
        <KwaiSelect :items="categories" />
      </KwaiField>
      <p class="uk-text-meta">
        {{ $t('form.team.category.hint')}}
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
import TeamCategory from '@/models/TeamCategory';
import Season from '@/models/Season';

import makeForm, { makeField, notEmpty } from '@/js/Form';
const makeTeamForm = (fields) => {
  const writeForm = (team) => {
    fields.name.value = team.name;
    fields.remark.value = team.remark;
    if (team.season) {
      fields.season.value = team.season.id;
    }
    if (team.team_category) {
      fields.category.value = team.team_category.id;
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
    if (fields.category.value === 0) {
      team.team_category = null;
    } else {
      team.team_category = new TeamCategory();
      team.team_category.id = fields.category.value;
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
        category: makeField({
          value: 0
        }),
        remark: makeField({
          value: ''
        })
      })
    };
  },
  computed: {
    team() {
      return this.$store.state.team.active;
    },
    error() {
      return this.$store.state.team.error;
    },
    seasons() {
      var seasons = this.$store.getters['team/season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('form.team.season.empty')
      });
      return seasons;
    },
    categories() {
      var categories = this.$store.getters['team/category/categoriesAsOptions'];
      categories.unshift({
        value: 0,
        text: this.$t('form.team.category.empty')
      });
      return categories;
    },
  },
  async created() {
    await this.$store.dispatch('team/season/browse');
    await this.$store.dispatch('team/category/browse');
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      vm.setupForm(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    this.setupForm(to.params);
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
    async setupForm(params) {
      if (params.id) {
        await this.$store.dispatch('team/read', {
          id: params.id
        });
        this.form.writeForm(this.team);
      } else {
        this.$store.dispatch('team/create');
      }
    },
    async submit() {
      this.form.clearErrors();
      this.form.readForm(this.team);
      try {
        await this.$store.dispatch('team/save', this.team);
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
