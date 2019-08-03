<template>
  <div class="kwai-card profile">
    <div class="kwai-card-image">
      <img :src="image" />
    </div>
    <div class="kwai-card-title">
      <h2>{{ coach.name }}</h2>
    </div>
    <div class="kwai-card-text">
      <h4>{{ coach.diploma }}</h4>
      <p class="uk-text-meta">{{ coach.description }}</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    coach: {
      type: Object,
      required: true
    }
  },
  computed: {
    hasImage() {
      return this.coach.image !== null;
    },
    image() {
      if (this.coach.image) {
        return this.coach.image;
      }
      return require('@/apps/members/images/no_avatar.png');
    },
  }
};
</script>
