<template>
  <li style="display:flex; justify-content: space-between;">
    <div>
      <span class="kwai-text-meta">
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
        {{ member.person.name }}
      </span>
      <del v-else>
        {{ member.person.name }}
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
