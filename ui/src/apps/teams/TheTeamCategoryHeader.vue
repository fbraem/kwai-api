<template>
  <Header
    v-if="teamCategory"
    :title="$t('categories')"
    :subtitle="teamCategory.name"
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
    teamCategory() {
      return this.$store.state.team.category.active;
    }
  }
};
</script>
