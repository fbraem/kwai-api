<template>
  <!-- eslint-disable max-len -->
  <div>
    <h1 class="text-xl header-line font-medium mb-0">
      Trainingsmoment
    </h1>
    <Spinner v-if="$wait.is('training.definitions.read')" />
    <Alert
      v-if="notAllowed"
      type="danger"
    >
        {{ $t('not_allowed') }}
    </Alert>
    <Alert
      v-if="notFound"
      type="danger"
    >
        {{ $t('training.definitions.not_found') }}
    </Alert>
    <div
      v-if="definition"
      class="p-2"
    >
      <Attributes :attributes="attributes">
        <template v-slot:value_season="{ attribute }">
          <router-link
            v-if="attribute.value"
            :to="{ name: 'seasons.read', params: { id : attribute.value.id} }"
          >
            {{ attribute.value.name }}
          </router-link>
        </template>
        <template v-slot:value_team="{ attribute }">
          <router-link
            v-if="attribute.value"
            :to="{ name: 'teams.read', params: { id : attribute.value.id} }"
          >
            {{ attribute.value.name }}
          </router-link>
        </template>
        <template v-slot:value_time="{ attribute }">
          <i class="far fa-clock"></i> {{ attribute.value }}
        </template>
      </Attributes>
      <div class="flex justify-between border-t mb-2 sm:mb-4 pt-3">
        <div class="flex flex-wrap text-xs">
          <div class="mr-4">
            <strong>Aangemaakt:</strong> {{ definition.localCreatedAt }}
          </div>
          <div>
            <strong>Laatst gewijzigd:</strong> {{ definition.localUpdatedAt }}
          </div>
        </div>
        <div>
          <IconButtons
            :toolbar="toolbar"
            normal-class="text-gray-700"
            hover-class="hover:bg-gray-300"
          />
        </div>
      </div>
      <TrainingGeneratorForm :definition="definition" />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import TrainingGeneratorForm from './TrainingGeneratorForm';
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import Attributes from '@/components/Attributes';
import IconButtons from '@/components/IconButtons';

export default {
  components: {
    Spinner,
    TrainingGeneratorForm,
    Alert,
    Attributes,
    IconButtons
  },
  i18n: messages,
  computed: {
    definition() {
      return this.$store.state.training.definition.active;
    },
    error() {
      return this.$store.state.training.definition.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    },
    attributes() {
      return {
        name: {
          label: this.$t('training.definitions.form.name.label'),
          value: this.definition.name
        },
        description: {
          label: this.$t('training.definitions.form.description.label'),
          value: this.definition.description
        },
        weekday: {
          label: this.$t('training.definitions.form.weekday.label'),
          value: this.definition.weekdayText
        },
        time: {
          label: this.$t('training.definitions.time'),
          value:
            this.definition.formattedStartTime
            + ' - '
            + this.definition.formattedEndTime
        },
        team: {
          label: this.$t('training.definitions.form.team.label'),
          value: this.definition.team
        },
        season: {
          label: this.$t('training.definitions.form.season.label'),
          value: this.definition.season
        },
        location: {
          label: this.$t('training.definitions.form.location.label'),
          value: this.definition.location
        },
        remark: {
          label: this.$t('training.definitions.form.remark.label'),
          value: this.definition.remark
        }
      };
    },
    toolbar() {
      const buttons = [
        {
          icon: 'fas fa-list',
          route: {
            name: 'trainings.definitions.browse'
          }
        },
      ];
      if (this.$can('update', this.definition)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'trainings.definitions.update',
            params: {
              id: this.definition.id
            }
          }
        });
      }
      return buttons;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.id);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.id);
    next();
  },
  methods: {
    fetchData(id) {
      this.$store.dispatch('training/definition/read', {
        id: id
      }).catch((err) => {
        console.log(err);
      });
    },
  }
};
</script>
