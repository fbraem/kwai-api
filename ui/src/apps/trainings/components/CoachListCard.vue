<template>
  <div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header">
      <h3 class="uk-card-title">Onze trainers</h3>
    </div>
    <div class="uk-card-body">
      <div
        class="uk-grid-small uk-grid-divider"
        uk-grid
      >
        <div
          v-for="coach in coaches"
          class="uk-width-1-1"
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
    return h('div', {}, [
      h('img', {
        class: {
          'uk-border-circle': true
        },
        attrs: {
          width: 32,
          height: 32,
          src: this.image
        }
      }),
      h('span', {
        class: {
          'uk-text-middle': true,
          'uk-margin-left': true
        },
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
