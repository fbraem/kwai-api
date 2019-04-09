<template>
  <div>
    <div
      v-if="$wait.is('members.browse')"
      class="uk-flex-center"
      uk-grid
    >
      <div class="uk-text-center">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
      </div>
    </div>
    <div
      v-else-if="members"
      uk-grid
    >
      <div
        v-if="members.length == 0"
        class="uk-alert uk-alert-warning"
      >
        {{ $t('no_members') }}
      </div>
      <div
        v-else
        class="uk-flex uk-flex-center uk-grid-small"
        uk-grid
      >
        <div
          v-for="(group, letter) in sortedMembers"
          :key="letter"
        >
          <span class="uk-label">
            <a
              class="uk-link-reset"
              @click="jumpIt('#letter-' + letter)">
              {{letter}}
            </a>
          </span>
        </div>
      </div>
      <div>
        <div class="uk-column-1-2@s uk-column-1-3@m uk-column-1-4@xl">
          <div
            v-for="(group, letter) in sortedMembers"
            :key="letter"
          >
            <h3
              class="uk-heading-bullet"
              :id="'letter-' + letter"
            >
              {{ letter }}
            </h3>
            <ul class="uk-list">
              <MemberListItem
                v-for="member in group"
                :member="member"
                :key="member.id"
              />
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'flag-icon-css/css/flag-icon.css';

import messages from './lang';
import jump from 'jump.js';

import MemberListItem from './MemberListItem.vue';

/**
 * Page for browsing a member
 */
export default {
  components: {
    MemberListItem
  },
  i18n: messages,
  computed: {
    members() {
      return this.$store.state.member.members;
    },
    count() {
      if (this.members) {
        return this.members.length;
      }
      return 0;
    },
    sortedMembers() {
      var result = {};
      if (this.members) {
        this.members.forEach((e) => {
          var firstChar = e.person.lastname.charAt(0).toUpperCase();
          if (!result[firstChar]) result[firstChar] = [];
          result[firstChar].push(e);
        });
      }
      return result;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('member/browse', {
        active: true
      });
    },
    jumpIt(target) {
      jump(target);
    }
  }
};
</script>
