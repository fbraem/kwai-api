<template>
  <div v-if="season" style="grid-area: page-content">
    <h1>
      {{ $t('season') }}
    </h1>
    <Attributes :attributes="attributes" />
    <Alert v-if="season.active" :icon="false">
      <i class="fas fa-check"></i>
      &nbsp;&nbsp;{{ $t('active_message') }}
    </Alert>
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
