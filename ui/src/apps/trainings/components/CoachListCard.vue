<template>
  <!-- eslint-disable max-len -->
  <div>
    <div class="shadow-md p-4">
      <h3>Onze trainers</h3>
      <div class="flex flex-wrap">
        <div
          class="p-3 w-full sm:w-1/2 md:w-1/3 lg:w-1/4"
          v-for="coach in coaches"
          :key="coach.id"
        >
          <CoachComponent :coach="coach" />
        </div>
      </div>
    </div>
  </div>
</template>

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
    return h('div', {
      style: {
        display: 'flex',
        'align-items': 'center'
      }
    }, [
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
      h('div', {
        style: {
          'vertical-align': 'middle',
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
