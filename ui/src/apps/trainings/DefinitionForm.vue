<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <form class="uk-form-stacked">
        <div uk-grid>
          <div class="uk-width-expand">
            <field name="name" :label="$t('training.definitions.form.name.label')">
              <uikit-input-text :placeholder="$t('training.definitions.form.name.placeholder')" />
            </field>
          </div>
          <div>
            <field name="active" :label="$t('training.definitions.form.active.label')">
              <uikit-switch />
            </field>
          </div>
        </div>
        <field name="description" :label="$t('training.definitions.form.description.label')">
          <uikit-textarea :rows="5" :placeholder="$t('training.definitions.form.description.placeholder')" />
        </field>
        <field name="weekday" :label="$t('training.definitions.form.weekday.label')">
          <uikit-select :items="weekdays" />
        </field>
        <div class="uk-child-width-1-2" uk-grid>
          <div>
            <field name="start_time" :label="$t('training.definitions.form.start_time.label')">
              <uikit-input-text :placeholder="$t('training.definitions.form.start_time.placeholder')" />
            </field>
          </div>
          <div>
            <field name="end_time" :label="$t('training.definitions.form.end_time.label')">
              <uikit-input-text :placeholder="$t('training.definitions.form.end_time.placeholder')" />
            </field>
          </div>
        </div>
        <field name="season" :label="$t('training.definitions.form.season.label')">
          <uikit-select :items="seasons" />
        </field>
        <field name="team" :label="$t('training.definitions.form.team.label')">
          <uikit-select :items="teams" />
        </field>
        <field name="location" :label="$t('training.definitions.form.location.label')">
          <uikit-input-text :placeholder="$t('training.definitions.form.location.placeholder')" />
        </field>
        <field name="remark" :label="$t('training.definitions.form.remark.label')">
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
</template>

<script>
import moment from 'moment';

import TrainingDefinition from '@/models/trainings/Definition';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSelect from '@/components/forms/Select.vue';
import UikitSwitch from '@/components/forms/Switch.vue';

import messages from './lang';

import DefinitionForm from './DefinitionForm';

export default {
  components: {
    Field, UikitInputText, UikitTextarea,
    UikitSelect, UikitSwitch
  },
  mixins: [ DefinitionForm ],
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
      return this.$store.state.training.definition.error;
    },
    seasons() {
      var seasons = this.$store.getters['season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('training.definitions.form.season.no_season')
      });
      return seasons;
    },
    teams() {
      var teams = this.$store.getters['team/teamsAsOptions'];
      teams.unshift({
        value: 0,
        text: this.$t('training.definitions.form.team.no_team')
      });
      return teams;
    },
  },
  async created() {
    await this.$store.dispatch('season/browse');
    await this.$store.dispatch('team/browse');
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
        = await this.$store.dispatch('training/definition/read', {
          id: id
        });
      this.writeForm(this.definition);
    },
    submit() {
      this.clearErrors();
      this.readForm(this.definition);
      this.$store.dispatch('training/definition/save', this.definition)
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
