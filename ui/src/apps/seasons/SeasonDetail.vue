<template>
  <div
    v-if="season"
    class="flex flex-col"
  >
    <h1>
      {{ $t('season') }}
    </h1>
    <Attributes :attributes="attributes" />
    <Alert v-if="season.active" :icon="false">
      <i class="fas fa-check"></i>
      &nbsp;&nbsp;{{ $t('active_message') }}
    </Alert>
    <div class="p-3 self-end">
      <router-link
        v-if="$can('update', season)"
        class="icon-button text-gray-700 hover:bg-gray-300"
        :to="{ name: 'seasons.update', params: { id: season.id } }"
      >
        <i class="fas fa-edit"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Attributes from '@/components/Attributes';
import Alert from '@/components/Alert';

export default {
  components: {
    Attributes, Alert
  },
  i18n: messages,
  computed: {
    season() {
      return this.$store.state.season.active;
    },
    attributes() {
      return {
        name: {
          label: this.$t('form.season.name.label'),
          value: this.season.name
        },
        start_date: {
          label: this.$t('form.season.start_date.label'),
          value: this.season.formatted_start_date
        },
        end_date: {
          label: this.$t('form.season.end_date.label'),
          value: this.season.formatted_end_date
        },
        remark: {
          label: this.$t('form.season.remark.label'),
          value: this.season.remark
        }
      };
    }
  }
};
</script>
