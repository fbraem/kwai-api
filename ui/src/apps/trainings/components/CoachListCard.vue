<template>
  <div>
    <div class="coach-list-card">
      <h3>Onze trainers</h3>
      <div class="coach-list">
        <div
          class="coach-item"
          v-for="coach in coaches"
          :key="coach.id"
        >
          <CoachComponent :coach="coach" />
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import '@/site/scss/_mq.scss';

.coach-list-card {
  box-shadow: 0 5px 15px rgba(0,0,0,.08);
  padding: 15px;
}
.coach-list {
  display: flex;
  flex-wrap: wrap;
}
.coach-item {
  padding: 10px;
  border-bottom: 1px solid var(--kwai-color-muted);

  @include mq($from: wide) {
    flex: 0 1 25%;
  }
  @include mq($from: tablet, $until: wide) {
    flex: 0 1 30%;
  }
  @include mq($from: mobile, $until: tablet) {
    flex: 0 1 100%;
  }
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
