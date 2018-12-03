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
                <uikit-input-text v-model="form.definition.name" :validator="$v.form.definition.name" :errors="nameErrors" id="name" :placeholder="$t('training.definitions.form.name.placeholder')">
                  {{ $t('training.definitions.form.name.label') }}:
                </uikit-input-text>
              </div>
              <div>
                <uikit-switch v-model="form.definition.active">
                  {{ $t('training.definitions.form.active.label') }}
                </uikit-switch>
              </div>
            </div>
            <uikit-textarea v-model="form.definition.description" :validator="$v.form.definition.description" :rows="5" id="description" :errors="descriptionErrors" :placeholder="$t('training.definitions.form.description.placeholder')">
              {{ $t('training.definitions.form.description.label') }}:
            </uikit-textarea>
            <uikit-select v-model="form.definition.weekday" :items="weekdays" :validator="$v.form.definition.weekday" :errors="weekdayErrors" id="weekday">
              {{ $t('training.definitions.form.weekday.label') }}:
            </uikit-select>
            <div class="uk-child-width-1-2" uk-grid>
              <div>
                <uikit-input-text v-model="form.definition.start_time" :validator="$v.form.definition.start_time" :errors="startTimeErrors" id="startTime" :placeholder="$t('training.definitions.form.start_time.placeholder')">
                  {{ $t('training.definitions.form.start_time.label') }}:
                </uikit-input-text>
              </div>
              <div>
                <uikit-input-text v-model="form.definition.end_time" :validator="$v.form.definition.end_time" :errors="endTimeErrors" id="endTime" :placeholder="$t('training.definitions.form.end_time.placeholder')">
                  {{ $t('training.definitions.form.end_time.label') }}:
                </uikit-input-text>
              </div>
            </div>
            <uikit-select
                v-model="form.definition.season"
                :items="seasons"
                :validator="$v.form.definition.season"
                :errors="seasonErrors"
                id="season">
                {{ $t('training.definitions.form.season.label') }}:
            </uikit-select>
            <uikit-input-text v-model="form.definition.location" :validator="$v.form.definition.location" :errors="locationErrors" id="location" :placeholder="$t('training.definitions.form.location.placeholder')">
              {{ $t('training.definitions.form.location.label') }}:
            </uikit-input-text>
            <uikit-textarea v-model="form.definition.remark" :validator="$v.form.definition.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('training.definitions.form.remark.placeholder')">
              {{ $t('training.definitions.form.remark.label') }}:
            </uikit-textarea>
          </form>
        </div>
        <div uk-grid class="uk-width-1-1">
          <div class="uk-width-expand">
          </div>
          <div class="uk-width-auto">
            <button class="uk-button uk-button-primary" :disabled="$v.$invalid" @click="submit">
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

import TrainingDefinition from '@/models/trainings/Definition';
import Season from '@/models/Season';

import { validationMixin } from 'vuelidate';
import { required, numeric } from 'vuelidate/lib/validators';
import isTime from '@/js/isTime';

import PageHeader from '@/site/components/PageHeader.vue';
import UikitInputText from '@/components/uikit/InputText.vue';
import UikitTextarea from '@/components/uikit/Textarea.vue';
import UikitSelect from '@/components/uikit/Select.vue';
import UikitSwitch from '@/components/uikit/Switch.vue';

import messages from './lang';

var initError = function() {
  return {
    name: [],
    description: [],
    season: [],
    weekday: [],
    start_time: [],
    end_time: [],
    active: [],
    location: [],
    remark: [],
  };
};
var initForm = function() {
  return {
    definition: {
      name: '',
      description: '',
      season: 0,
      weekday: 1,
      start_time: '',
      end_time: '',
      active: true,
      location: null,
      remark: ''
    }
  };
};

export default {
  components: {
    PageHeader, UikitInputText, UikitTextarea,
    UikitSelect, UikitSwitch
  },
  i18n: messages,
  mixins: [ validationMixin ],
  data() {
    return {
      definition: new TrainingDefinition(),
      form: initForm(),
      errors: initError(),
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
      var seasons = this.$store.getters['seasonModule/seasons'];
      if (seasons) {
        seasons = seasons.map((season) => ({
          value: season.id,
          text: season.name }
        ));
      } else {
        seasons = [];
      }
      seasons.unshift({
        value: 0,
        text: this.$t('training.definitions.form.season.no_season')
      });
      return seasons;
    },
    nameErrors() {
      const errors = [...this.errors.name];
      if (!this.$v.form.definition.name.$dirty) return errors;
      !this.$v.form.definition.name.required
        && errors.push(this.$t('training.definitions.form.name.required'));
      return errors;
    },
    descriptionErrors() {
      const errors = [...this.errors.description];
      if (!this.$v.form.definition.description.$dirty) return errors;
      !this.$v.form.definition.description.required
        && errors.push(
          this.$t('training.definitions.form.description.required')
        );
      return errors;
    },
    weekdayErrors() {
      const errors = [...this.errors.weekday];
      if (!this.$v.form.definition.weekday.$dirty) return errors;
      !this.$v.form.definition.weekday.required
        && errors.push(this.$t('training.definitions.form.weekday.required'));
      !this.$v.form.definition.weekday.numeric
        && errors.push(this.$t('training.definitions.form.weekday.numeric'));
      return errors;
    },
    startTimeErrors() {
      const errors = [...this.errors.start_time];
      if (!this.$v.form.definition.start_time.$dirty) return errors;
      !this.$v.form.definition.start_time.required
        && errors.push(
          this.$t('training.definitions.form.start_time.required')
        );
      if (!this.$v.form.definition.start_time.isTime)
        errors.push(this.$t('training.definitions.form.start_time.invalid'));
      return errors;
    },
    endTimeErrors() {
      const errors = [...this.errors.end_time];
      if (!this.$v.form.definition.end_time.$dirty) return errors;
      !this.$v.form.definition.end_time.required
        && errors.push(this.$t('training.definitions.form.end_time.required'));
      if (!this.$v.form.definition.end_time.isTime)
        errors.push(this.$t('training.definitions.form.end_time.invalid'));
      return errors;
    },
    seasonErrors() {
      const errors = [...this.errors.season];
      if (!this.$v.form.definition.season.$dirty) return errors;
      return errors;
    },
    locationErrors() {
      const errors = [...this.errors.location];
      if (!this.$v.form.definition.location.$dirty) return errors;
      return errors;
    },
    remarkErrors() {
      const errors = [...this.errors.remark];
      if (!this.$v.form.definition.remark.$dirty) return errors;
      return errors;
    }
  },
  validations: {
    form: {
      definition: {
        name: { required},
        description: { required },
        weekday: { required, numeric },
        start_time: { required, isTime },
        end_time: { required, isTime },
        season: {},
        location: {},
        remark: {}
      }
    }
  },
  beforeCreate() {
    if (!this.$store.state.trainingDefinitionModule) {
      this.$store.registerModule(
        'trainingDefinitionModule', trainingDefinitionStore
      );
    }
    if (!this.$store.state.seasonModule) {
      this.$store.registerModule('seasonModule', seasonStore);
    }
  },
  async created() {
    await this.$store.dispatch('seasonModule/browse');
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
          nv.response.data.errors.forEach((item, index) => {
            if (item.source && item.source.pointer) {
              var attr = item.source.pointer.split('/').pop();
              this.errors[attr].push(item.title);
            }
          });
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
      this.$v.$reset();
      this.form = initForm();
    },
    async fetchData(id) {
      this.teamType = await this.$store.dispatch('teamTypeModule/read', {
        id: id
      });
      this.fillForm();
    },
    fillForm() {
      this.form.definition.name = this.definition.name;
      this.form.definition.description = this.definition.description;
      this.form.definition.active = this.definition.active;
      this.form.definition.location = this.definition.location;
      this.form.definition.start_time = this.definition.start_time;
      this.form.definition.end_time = this.definition.end_time;
      if (this.definition.season) {
        this.form.definition.season = this.definition.season.id;
      }
      this.form.definition.remark = this.definition.remark;
    },
    fillDefinition() {
      this.definition.name = this.form.definition.name;
      this.definition.description = this.form.definition.description;
      this.definition.active = this.form.definition.active;
      this.definition.weekday = this.form.definition.weekday;
      this.definition.location = this.form.definition.location;
      this.definition.start_time = this.form.definition.start_time;
      this.definition.end_time = this.form.definition.end_time;
      this.definition.remark = this.form.definition.remark;
      if (this.form.definition.season) {
        if (this.form.definition.season === 0) {
          this.definition.season = null;
        } else {
          this.definition.season = new Season();
          this.definition.season.id = this.form.definition.season;
        }
      }
    },
    submit() {
      this.errors = initError();
      this.fillDefinition();
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
