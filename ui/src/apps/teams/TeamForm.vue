<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <h1>{{ $t('teams') }}</h1>
      <h3 v-if="creating" class="uk-h3 uk-margin-remove">
        {{ $t('create') }}
      </h3>
      <h3 v-else class="uk-h3 uk-margin-remove">
        {{ $t('update') }}
      </h3>
    </PageHeader>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <div uk-grid>
        <div class="uk-width-1-1">
          <form class="uk-form-stacked">
            <field name="name" :label="$t('form.team.name.label')">
              <uikit-input-text :placeholder="$t('form.team.name.placeholder')">
              </uikit-input-text>
            </field>
            <field name="season" :label="$t('form.team.season.label')">
              <uikit-select :items="seasons">
              </uikit-select>
            </field>
            <p class="uk-text-meta">{{ $t('form.team.season.hint')}}</p>
            <field name="team_type" :label="$t('form.team.team_type.label')">
              <uikit-select :items="team_types">
              </uikit-select>
            </field>
            <p class="uk-text-meta">{{ $t('form.team.team_type.hint')}}</p>
            <field name="remark" :label="$t('form.team.remark.label')">
              <uikit-textarea :rows="5" id="remark"
                :placeholder="$t('form.team.remark.placeholder')">
              </uikit-textarea>
            </field>
          </form>
        </div>
        <div uk-grid class="uk-width-1-1">
          <div class="uk-width-expand">
          </div>
          <div class="uk-width-auto">
            <button class="uk-button uk-button-primary" :disabled="!$valid" @click="submit">
              <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import Team from '@/models/Team';

import PageHeader from '@/site/components/PageHeader.vue';

import TeamForm from './TeamForm';
import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSelect from '@/components/forms/Select.vue';

import messages from './lang';

import teamStore from '@/stores/teams';
import teamTypeStore from '@/stores/team_types';
import seasonStore from '@/stores/seasons';
import registerModule from '@/stores/mixin';

export default {
  components: {
    PageHeader, Field, UikitInputText, UikitTextarea, UikitSelect
  },
  i18n: messages,
  mixins: [
    TeamForm,
    registerModule(
      {
        teamType: teamTypeStore
      },
      {
        team: teamStore
      },
      {
        season: seasonStore
      }
    ),
  ],
  data() {
    return {
      team: new Team()
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
      this.writeForm(this.team);
    },
    async submit() {
      this.clearErrors();
      this.readForm(this.team);
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
