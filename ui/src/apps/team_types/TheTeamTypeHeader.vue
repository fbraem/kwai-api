<template>
  <Header
    v-if="teamtype"
    :title="$t('types')"
    :subtitle="teamtype.name"
    :toolbar="toolbar"
  />
</template>

<script>
import messages from './lang';
import Header from '@/components/Header';

export default {
  components: {
    Header
  },
  i18n: messages,
  computed: {
    teamtype() {
      return this.$store.getters['teamType/type'](this.$route.params.id);
    },
    toolbar() {
      const buttons = [{
        icon: 'fas fa-list',
        route: {
          name: 'team_types.browse'
        }
      }];
      if (this.$can('update', this.teamtype)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'team_types.update',
            params: {
              id: this.teamtype.id
            }
          }
        });
      }
      return buttons;
    }
  }
};
</script>
