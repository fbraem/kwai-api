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
      <IconButtons
        :toolbar="toolbar"
        normal-class="text-gray-700"
        hover-class="hover:bg-gray-300"
      />
    </div>
  </div>
</template>

<script>
import messages from './lang';

import Attributes from '@/components/Attributes';
import Alert from '@/components/Alert';
import IconButtons from '@/components/IconButtons';

export default {
  components: {
    Attributes, Alert, IconButtons
  },
  i18n: messages,
  computed: {
    season() {
      return this.$store.state.season.active;
    },
    toolbar() {
      const buttons = [
        {
          icon: 'fas fa-list',
          route: {
            name: 'seasons.browse'
          }
        },
      ];
      if (this.$can('update', this.season)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'seasons.update',
            params: {
              id: this.season.id
            }
          }
        });
      }
      return buttons;
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
