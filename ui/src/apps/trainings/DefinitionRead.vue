<template>
  <!-- eslint-disable max-len -->
  <div class="container mx-auto py-3 px-3 sm:px-0">
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
    <Spinner v-if="$wait.is('training.definitions.read')" />
    <SimpleCard
      v-if="definition"
      :title="definition.name"
      :note="definition.description"
      :toolbar="toolbar"
      class="mb-6"
    >
      <Attributes :attributes="attributes">
        <template v-slot:value="{prop, attribute}">
          <template v-if="prop === 'season'">
            <router-link
              v-if="attribute.value"
              :to="{ name: 'seasons.read', params: { id : attribute.value.id} }"
            >
              {{ attribute.value.name }}
            </router-link>
          </template>
          <template v-else-if="prop === 'team'">
            <router-link
              v-if="attribute.value"
              :to="{ name: 'teams.read', params: { id : attribute.value.id} }"
            >
              {{ attribute.value.name }}
            </router-link>
          </template>
          <span v-else>
            {{ attribute.value }}
          </span>
        </template>
      </Attributes>
      <template slot="footer">
        <div class="mr-4">
          <strong>Aangemaakt:</strong> {{ definition.localCreatedAt }}
        </div>
        <div>
          <strong>Laatst gewijzigd:</strong> {{ definition.localUpdatedAt }}
        </div>
      </template>
    </SimpleCard>
    <TrainingGeneratorForm :definition="definition" />
  </div>
</template>

<script>
import messages from './lang';

import TrainingGeneratorForm from './TrainingGeneratorForm';
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';
import SimpleCard from '@/components/SimpleCard';
import Attributes from '@/components/Attributes';

export default {
  components: {
    Spinner,
    TrainingGeneratorForm,
    Alert,
    SimpleCard,
    Attributes
  },
  i18n: messages,
  computed: {
    definition() {
      return this.$store.getters['training/definition/definition'](
        this.$route.params.id
      );
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
        location: {
          label: this.$t('training.definitions.form.location.label'),
          value: this.definition.name
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
        remark: {
          label: this.$t('training.definitions.form.remark.label'),
          value: this.definition.remark
        }
      };
    },
    toolbar() {
      const buttons = [];
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
