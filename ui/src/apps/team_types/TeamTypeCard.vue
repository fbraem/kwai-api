<template>
  <!-- eslint-disable max-len -->
  <div class="h-full flex flex-col border border-gray-500 shadow-lg overflow-hidden bg-tatami rounded">
    <div class="px-6 py-4 text-white">
      <h4>{{ type.name }}</h4>
      <p
        v-if="type.remark"
        class="text-xs truncate m-0 p-0"
      >
        {{ type.remark }}
      </p>
    </div>
    <div class="border-1 bg-white flex-grow">
      <Attributes :attributes="attributes">
        <template v-slot:value="{prop, attribute}">
          <i v-if="prop === 'competition' || prop === 'active'"
            class="fas fa-check"
            :class="{'fa-check': attribute.value, 'fa-times': !attribute.value,'text-red-700': !attribute.value }"
          >
          </i>
          <span v-else>
            {{ attribute.value }}
          </span>
        </template>
      </Attributes>
    </div>
    <div class="text-white flex flex-wrap justify-between px-3 py-2">
      <div class="text-xs flex flex-wrap items-center">
        <div>
          <strong>Aangemaakt:</strong> {{ type.localCreatedAt }}&nbsp;&nbsp;
        </div>
        <div>
          <strong>Laatst gewijzigd:</strong> {{ type.localUpdatedAt }}
        </div>
      </div>
      <div class="ml-auto">
        <router-link
          v-if="$can('update', type)"
          class="icon-button text-gray-700 hover:bg-gray-300"
          :to="{ name : 'team_types.update', params : { id : type.id } }"
        >
          <i class="fas fa-edit"></i>
        </router-link>
        <router-link
          v-if="$route.name !== 'team_types.read'"
          class="icon-button text-gray-700 hover:bg-gray-300"
          :to="{ name: 'team_types.read', params: { id : type.id} }"
        >
          <i class="fas fa-search"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Attributes from '@/components/Attributes';

export default {
  components: {
    Attributes
  },
  props: {
    type: {
      type: Object,
      required: true
    }
  },
  i18n: messages,
  computed: {
    gender() {
      var gender = this.type.gender;
      if (gender === 0) {
        return this.$t('no_restriction');
      } else if (gender === 1) {
        return this.$t('male');
      } else {
        return this.$t('female');
      }
    },
    attributes() {
      return {
        age: {
          label: this.$t('age'),
          value: `${this.type.start_age} - ${this.type.end_age}`
        },
        gender: {
          label: this.$t('gender'),
          value: this.gender
        },
        competition: {
          label: this.$t('competition'),
          value: this.type.competition
        },
        active: {
          label: this.$t('active'),
          value: this.type.active
        }
      };
    }
  }
};
</script>
