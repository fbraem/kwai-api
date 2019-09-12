<template>
  <div style="display: flex; flex-direction: column; align-items: center;">
    <div>
      <img
        style="border-radius: 100%;"
        :src="image"
      />
    </div>
    <div>
      <h2>{{ coach.name }}</h2>
    </div>
    <div>
      <h4 style="text-align: center;">{{ coach.diploma }}</h4>
      <p class="kwai-text-meta">{{ coach.description }}</p>
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
