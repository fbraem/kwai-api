<template>
  <div
    class="uk-grid-small"
    uk-grid
  >
    <div class="uk-width-1-1">
      <i :class="genderIconClass"></i>&nbsp;
      {{ member.person.name }}
    </div>
    <div class="uk-width-1-1 uk-margin-remove uk-text-meta">
      {{ member.license }} &bull; {{ member.person.formatted_birthdate }}
      ({{ memberAge }})
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  props: {
    member: {
      type: Object,
      required: true
    },
    date: {
      default: () => moment()
    }
  },
  computed: {
    genderIconClass() {
      return {
        fas: true,
        'fa-male': this.member.person.gender === 1,
        'fa-female': this.member.person.gender === 2,
        'fa-question': this.member.person.gender === 0,
      };
    },
    memberAge() {
      return this.date.diff(this.member.person.birthdate, 'years');
    }
  }
};
</script>
