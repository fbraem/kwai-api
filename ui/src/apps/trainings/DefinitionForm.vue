<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <h1>{{ $t('training.definitions.title') }}</h1>
      <h3 v-if="creating" class="uk-h3 uk-margin-remove">{{ $t('create') }}</h3>
      <h3 v-else class="uk-h3 uk-margin-remove">{{ $t('update') }}</h3>
    </PageHeader>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <div uk-grid>
        <div class="uk-width-1-1">
          <form class="uk-form-stacked">
            <div uk-grid>
              <div class="uk-width-expand">
                <field name="name">
                  <uikit-input-text :placeholder="$t('training.definitions.form.name.placeholder')" />
                </field>
              </div>
              <div>
                <field name="active">
                  <uikit-switch />
                </field>
              </div>
            </div>
            <field name="description">
              <uikit-textarea :rows="5" :placeholder="$t('training.definitions.form.description.placeholder')" />
            </field>
            <field name="weekday">
              <uikit-select :items="weekdays" />
            </field>
            <div class="uk-child-width-1-2" uk-grid>
              <div>
                <field name="start_time">
                  <uikit-input-text :placeholder="$t('training.definitions.form.start_time.placeholder')" />
                </field>
              </div>
              <div>
                <field name="end_time">
                  <uikit-input-text :placeholder="$t('training.definitions.form.end_time.placeholder')" />
                </field>
              </div>
            </div>
            <field name="season">
              <uikit-select :items="seasons" />
            </field>
            <field name="team">
              <uikit-select :items="teams" />
            </field>
            <field name="location">
              <uikit-input-text :placeholder="$t('training.definitions.form.location.placeholder')" />
            </field>
            <field name="remark">
              <uikit-textarea :rows="5" :placeholder="$t('training.definitions.form.remark.placeholder')" />
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
import moment from 'moment';

import trainingDefinitionStore from '@/stores/training/definitions';
import seasonStore from '@/stores/seasons';
import teamStore from '@/stores/teams';
import registerModule from '@/stores/mixin';

import TrainingDefinition from '@/models/trainings/Definition';

import PageHeader from '@/site/components/PageHeader.vue';
import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSelect from '@/components/forms/Select.vue';
import UikitSwitch from '@/components/forms/Switch.vue';

import messages from './lang';

import DefinitionForm from './DefinitionForm';

export default {
  components: {
    PageHeader, Field, UikitInputText, UikitTextarea,
    UikitSelect, UikitSwitch
  },
  mixins: [
    DefinitionForm,
    registerModule([
      {
        namespace: 'trainingDefinitionModule',
        store: trainingDefinitionStore
      },
      {
        namespace: 'seasonModule',
        store: seasonStore
      },
      {
        namespace: 'teamModule',
        store: teamStore
      },
    ]),
  ],
  i18n: messages,
  data() {
    return {
      definition: new TrainingDefinition(),
      weekdays: moment.weekdays(true).map((d, i) => {
        return {
          value: i + 1,
          text: d
        };
      })
    };
  },
  computed: {
    creating() {
      return this.definition != null && this.definition.id == null;
    },
    error() {
      return this.$store.getters['trainingDefinitionModule/error'];
    },
    seasons() {
      var seasons = this.$store.getters['seasonModule/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('training.definitions.form.season.no_season')
      });
      return seasons;
    },
    teams() {
      var teams = this.$store.getters['teamModule/teamsAsOptions'];
      teams.unshift({
        value: 0,
        text: this.$t('training.definitions.form.team.no_team')
      });
      return teams;
    },
  },
  async created() {
    await this.$store.dispatch('seasonModule/browse');
    await this.$store.dispatch('teamModule/browse');
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params.id);
      next();
    });
  },
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404){
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    clear() {
      // this.$v.$reset();
      // this.form.reset();
    },
    async fetchData(id) {
      this.definition
        = await this.$store.dispatch('trainingDefinitionModule/read', {
          id: id
        });
      this.writeForm(this.definition);
    },
    submit() {
      this.clearErrors();
      this.readForm(this.definition);
      this.$store.dispatch('trainingDefinitionModule/save', this.definition)
        .then((newDefinition) => {
          this.$router.push({
            name: 'trainings.definitions.read',
            params: { id: newDefinition.id }
          });
        });
    }
  }
};
</script>
