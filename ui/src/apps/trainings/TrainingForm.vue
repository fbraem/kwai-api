<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader>
      <h1>{{ $t('training.events.title') }}</h1>
      <h3 v-if="creating" class="uk-h3 uk-margin-remove">{{ $t('training.events.create') }}</h3>
      <h3 v-else class="uk-h3 uk-margin-remove">{{ $t('training.events.update') }}</h3>
    </PageHeader>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
      <div uk-grid>
        <div class="uk-width-1-1">
          <form class="uk-form-stacked">
            <div uk-grid>
              <div class="uk-width-expand">
                <field name="title" :label="$t('training.events.form.title.label')">
                  <uikit-input-text :placeholder="$t('training.events.form.title.placeholder')" />
                </field>
              </div>
              <div>
                <field name="active" :label="$t('training.events.form.active.label')">
                  <uikit-switch />
                </field>
              </div>
              <div>
                <field name="cancelled" :label="$t('training.events.form.cancelled.label')">
                  <uikit-switch />
                </field>
              </div>
            </div>
            <field name="summary" :label="$t('training.events.form.summary.label')">
              <uikit-textarea :rows="5" :placeholder="$t('training.events.form.summary.placeholder')" />
            </field>
            <div class="uk-child-width-1-3" uk-grid>
              <div>
                <field name="start_date" :label="$t('training.events.form.start_date.label')">
                  <uikit-input-text :placeholder="$t('training.events.form.start_date.placeholder')" />
                </field>
              </div>
              <div>
                <field name="start_time" :label="$t('training.events.form.start_time.label')">
                  <uikit-input-text :placeholder="$t('training.events.form.start_time.placeholder')" />
                </field>
              </div>
              <div>
                <field name="end_time" :label="$t('training.events.form.end_time.label')">
                  <uikit-input-text :placeholder="$t('training.events.form.end_time.placeholder')" />
                </field>
              </div>
            </div>
            <field name="season" :label="$t('training.events.form.season.label')">
              <uikit-select :items="seasons" />
            </field>
            <field name="teams" :label="$t('training.events.form.teams.label')">
              <multiselect :options="teams" label="name"
                track-by="id" :multiple="true" :close-on-select="false"
                :selectLabel="$t('training.events.form.teams.selectLabel')"
                :deselectLabel="$t('training.events.form.teams.deselectLabel')">
              </multiselect>
            </field>
            <field name="coaches" :label="$t('training.events.form.coaches.label')">
              <multiselect :options="coaches" label="name"
                track-by="id" :multiple="true" :close-on-select="false"
                :selectLabel="$t('training.events.form.coaches.selectLabel')"
                :deselectLabel="$t('training.events.form.coaches.deselectLabel')">
              </multiselect>
            </field>
            <field name="remark" :label="$t('training.events.form.remark.label')">
              <uikit-textarea :rows="5" :placeholder="$t('training.events.form.remark.placeholder')" />
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
import trainingStore from '@/stores/training';
import coachStore from '@/stores/training/coaches';
import seasonStore from '@/stores/seasons';
import teamStore from '@/stores/teams';
import registerModule from '@/stores/mixin';

import Training from '@/models/trainings/Training';

import PageHeader from '@/site/components/PageHeader.vue';
import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSelect from '@/components/forms/Select.vue';
import UikitSwitch from '@/components/forms/Switch.vue';
import Multiselect from '@/components/forms/MultiSelect.vue';

import messages from './lang';

import TrainingForm from './TrainingForm';

export default {
  components: {
    PageHeader,
    Field,
    UikitInputText,
    UikitSwitch,
    UikitSelect,
    UikitTextarea,
    Multiselect
  },
  i18n: messages,
  mixins: [
    TrainingForm,
    registerModule(
      {
        training: trainingStore
      },
      {
        training: trainingStore,
        coach: coachStore,
      },
      {
        season: seasonStore
      },
      {
        team: teamStore
      }
    ),
  ],
  data() {
    return {
      training: new Training()
    };
  },
  computed: {
    creating() {
      return this.training != null && this.training.id == null;
    },
    error() {
      return this.$store.state.training.error;
    },
    seasons() {
      var seasons = this.$store.getters['season/seasonsAsOptions'];
      seasons.unshift({
        value: 0,
        text: this.$t('training.events.form.season.no_season')
      });
      return seasons;
    },
    coaches() {
      return this.$store.state.training.coach.coaches || [];
    },
    teams() {
      return this.$store.state.team.teams || [];
    },
  },
  async created() {
    await this.$store.dispatch('season/browse');
    await this.$store.dispatch('training/coach/browse');
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
    async fetchData(id) {
      this.training
        = await this.$store.dispatch('training/read', {
          id: id
        });
      this.writeForm(this.training);
    },
    submit() {
      this.clearErrors();
      this.readForm(this.training);
      this.$store.dispatch('training/save', this.training)
        .then((newTraining) => {
          this.$router.push({
            name: 'trainings.read',
            params: { id: newTraining.id }
          });
        });
    }
  }
};
</script>
