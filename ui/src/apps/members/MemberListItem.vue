<template>
  <li class="flex justify-between">
    <div>
      <span class="text-sm">
        {{ member.license }}
      </span>
      <span>&nbsp;-&nbsp;</span>
      <i
        v-if="member.person.isMale"
        class="fas fa-male"
        style="width:8px"
      >
      </i>
      <i
        v-if="member.person.isFemale"
        class="fas fa-female"
        style="width:8px"
      >
      </i>
      &nbsp;-&nbsp;
      <span v-if="member.active">
        <router-link :to="{ name: 'members.read', params: { id: member.id }}">
          {{ member.person.name }}
        </router-link>
      </span>
      <del v-else>
        <router-link :to="{ name: 'members.read', params: { id: member.id }}">
          {{ member.person.name }}
        </router-link>
      </del>
      &nbsp;({{ member.person.age }})
    </div>
    <div>
      <span
        :class="[ flagClass ]"
        class="flag-icon"
      >
      </span>
    </div>
  </li>
</template>

<script>
import Member from '@/models/Member';

export default {
  props: {
    member: {
      type: Member,
      required: true
    }
  },
  data() {
    return {
      flagClass: 'flag-icon-'
        + this.member.person.nationality.iso_2.toLowerCase()
    };
  }
};
</script>
