<template>
  <div class="sm:flex items-center rounded-lg p-6 shadow-md">
    <img
      class="h-16 w-16 sm:h-48 sm:w-48 mx-auto sm:mx-0 sm:mr-6 rounded-full"
      :src="image"
    />
    <div class="sm:flex-none text-center sm:text-left">
      <h2>{{ coach.name }}</h2>
      <h4>{{ coach.diploma }}</h4>
      <p class="text-sm">{{ coach.description }}</p>
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
