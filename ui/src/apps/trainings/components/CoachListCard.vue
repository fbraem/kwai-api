<template>
  <div class="coach-list-card">
    <h3>Onze trainers</h3>
    <div
      class="coach-list"
      v-for="coach in coaches"
      :key="coach.id"
    >
      <CoachComponent :coach="coach" />
    </div>
  </div>
</template>

<style>
.coach-list-card {
  display: flex;
  box-shadow: 0 5px 15px rgba(0,0,0,.08);
  flex-direction: column;
  padding: 15px;
}
.coach-list {
  padding: 10px;
}
.coach-list:not(:last-child) {
  border-bottom: 1px solid var(--kwai-color-muted);
}
</style>

<script>

import Coach from '@/models/trainings/Coach';

const CoachComponent = {
  props: {
    coach: {
      type: Coach,
      required: true
    }
  },
  computed: {
    image() {
      return require('@/apps/members/images/no_avatar.png');
    },
    link() {
      return {
        name: 'trainings.coaches.read',
        params: {
          id: this.coach.id
        }
      };
    }
  },
  render(h) {
    return h('div', {}, [
      h('img', {
        style: {
          'border-radius': '100%'
        },
        attrs: {
          width: 32,
          height: 32,
          src: this.image
        }
      }),
      h('span', {
        style: {
          'text-align': 'center',
          'margin-left': '20px'
        }
      }, [
        h('router-link', {
          attrs: {
            to: this.link
          }
        }, this.coach.name),
      ]),
    ]);
  }
};

export default {
  props: {
    coaches: {
      type: Array,
      required: true
    }
  },
  components: {
    CoachComponent
  },
  computed: {
    image() {
      return require('@/apps/members/images/no_avatar.png');
    }
  }
};
</script>
