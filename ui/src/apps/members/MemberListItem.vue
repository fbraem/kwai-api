<template>
  <li>
    <span class="uk-text-meta">
      {{ member.license }}
    </span> -
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
    -
    <span v-if="member.active">
      {{ member.person.name }}
    </span>
    <del v-else>
      {{ member.person.name }}
    </del>
    &nbsp;({{ member.person.age }})
    <span
      :class="[ flagClass ]"
      class="flag-icon uk-float-right"
    >
    </span>
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
