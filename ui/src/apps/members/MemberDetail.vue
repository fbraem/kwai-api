<template>
  <!-- eslint-disable max-len -->
  <div
    v-if="member"
    class="container mx-auto p-4"
  >
    <h1>Details</h1>
    <Alert type="warning">
      <strong>Opgelet!</strong> Gebruik deze gegevens enkel in functie van onze club.
    </Alert>
    <dl class="mb-2">
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Naam
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.person.name }}
        </dd>
      </div>
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Geboortedatum
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.person.formatted_birthdate }}
          <span class="text-sm">({{ member.person.age }} jr)</span>
        </dd>
      </div>
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Geslacht
        </dt>
        <dd class="w-full sm:w-2/3">
          <span v-if="member.person.isMale">
            <i class="fas fa-male"></i> Man
          </span>
          <span v-if="member.person.isFemale">
            <i class="fas fa-female"></i> Vrouw
          </span>
        </dd>
      </div>
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Licentie
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.license }}
          <i18n path="license_date" tag="span" class="text-sm">
            <span :class="licenseDateClass">
              {{ member.formatted_license_end_date }}</span>
          </i18n>
        </dd>
      </div>
      <div class="flex flex-wrap p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Nationaliteit
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.person.nationality.iso_3 }}&nbsp;&nbsp;
          <i
            :class="flagClass"
            class="flag-icon"
          >
          </i>
        </dd>
      </div>
    </dl>
    <h1>Contact Gegevens</h1>
    <dl>
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Adres
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.person.contact.address }}<br/>
          {{ member.person.contact.postal_code}}
          {{ member.person.contact.city }}<br />
          {{ member.person.contact.country.name }}
        </dd>
      </div>
      <div
        v-if="member.person.contact.email"
        class="flex flex-wrap border-b p-3"
      >
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Email
        </dt>
        <dd class="w-full sm:w-2/3">
          <a :href="'mailto:' + member.person.contact.email">
            {{ member.person.contact.email }}
          </a>
        </dd>
      </div>
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          GSM
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.person.contact.mobile }}
        </dd>
      </div>
      <div class="flex flex-wrap border-b p-3">
        <dt class="w-full sm:w-1/3 sm:pr-6 font-bold md:text-right">
          Tel.
        </dt>
        <dd class="w-full sm:w-2/3">
          {{ member.person.contact.tel }}
        </dd>
      </div>
    </dl>
  </div>
</template>

<script>
import Alert from '@/components/Alert';

import messages from './lang';

export default {
  components: {
    Alert
  },
  i18n: messages,
  computed: {
    member() {
      return this.$store.state.member.selected;
    },
    licenseDateClass() {
      return {
        'text-red-700': this.member.license_ended,
        'font-bold': this.member.license_ended
      };
    },
    flagClass() {
      return 'flag-icon-' + this.member.person.nationality.iso_2.toLowerCase();
    },
  }
};
</script>
