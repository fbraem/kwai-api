<template>
  <!-- eslint-disable max-len -->
  <SimpleCard
    :toolbar="toolbar"
    :title="team.name"
    :note="team.remark"
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
        <template v-else-if="prop === 'team_type'">
          <router-link
            v-if="attribute.value"
            :to="{ name: 'team_types.read', params: { id : attribute.value.id} }"
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
      <div>
        <strong>Aangemaakt:</strong> {{ team.localCreatedAt }}
      </div>
      <div>
        <strong>Laatst gewijzigd:</strong> {{ team.localUpdatedAt }}
      </div>
    </template>
  </SimpleCard>
</template>

<script>
import messages from './lang';

import SimpleCard from '@/components/SimpleCard';
import Attributes from '@/components/Attributes';

export default {
  i18n: messages,
  components: {
    SimpleCard,
    Attributes,
  },
  props: {
    team: {
      type: Object,
      required: true
    },
    complete: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    toolbar() {
      const buttons = [];
      if (this.$can('update', this.team)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'teams.update',
            params: {
              id: this.team.id
            }
          }
        });
      }
      if (this.$route.name !== 'teams.read') {
        buttons.push({
          icon: 'fas fa-search',
          route: {
            name: 'teams.read',
            params: {
              id: this.team.id
            }
          }
        });
      }
      return buttons;
    },
    attributes() {
      const importantAttributes = {
        name: {
          label: this.$t('form.team.name.label'),
          value: this.team.name
        },
        season: {
          label: this.$t('form.team.season.label'),
          value: this.team.season
        },
        members: {
          label: this.$t('members'),
          value: this.team.members_count
        }
      };
      if (!this.complete) return importantAttributes;
      const otherAttributes = {
        team_type: {
          label: this.$t('form.team.team_type.label'),
          value: this.team.team_type
        }
      };
      return { ...importantAttributes, ...otherAttributes };
    }
  }
};
</script>
