<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <Spinner style="grid-column: span 2;" v-if="$wait.is('members.browse')" />
    <div
      style="grid-column: span 2;"
      v-else-if="members"
    >
      <div
        v-if="members.length == 0"
        class="warning:kwai-alert"
      >
        {{ $t('no_members') }}
      </div>
      <div
        style="display: flex; align-items: center;justify-content: center; flex-wrap: wrap;"
        v-else
      >
        <div
          v-for="(group, letter) in sortedMembers"
          style="margin-right: 10px;"
          :key="letter"
        >
          <span class="primary:kwai-badge">
            <a
              class="kwai-link-reset"
              @click="jumpIt('#letter-' + letter)">
              {{letter}}
            </a>
          </span>
        </div>
      </div>
      <div style="column-count: 3; column-gap: 30px;margin-top: 30px;">
        <div
          v-for="(group, letter) in sortedMembers"
          :key="letter"
        >
          <h3 style="border-left: 8px solid var(--kwai-color-muted);padding-left: 8px;"
            :id="'letter-' + letter"
          >
            {{ letter }}
          </h3>
          <ul class="kwai-list">
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
</template>

<script>
import 'flag-icon-css/css/flag-icon.css';

import messages from './lang';
import jump from 'jump.js';

import Spinner from '@/components/Spinner';
import MemberListItem from './MemberListItem';

/**
 * Page for browsing a member
 */
export default {
  components: {
    Spinner,
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
