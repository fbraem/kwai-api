<template>
  <div class="bg-gray-800">
    <div class="container mx-auto">
      <div class="flex flex-wrap text-gray-300">

        <!-- ABOUT COLUMN -->
        <div class="w-full md:w-1/3 p-5 border-r border-gray-700 text-center">
          <h1><span class="uppercase">Judokwai Kemzeke</span> vzw</h1>
          <p>
            {{ $t('message.info') }}
          </p>
        </div>

        <!-- CONTACT US -->
        <div class="w-full md:w-1/3 p-5 border-r border-gray-700 text-center">
          <h1 class="uppercase">Contacteer ons</h1>
          <p>
            <strong>Judokwai Kemzeke vzw</strong><br />
            <i class="fas fa-home"></i> Polderstraat 10<br />
            9190 Stekene<br />
          </p>
          <div class="relative w-20 h-20 mx-auto my-12 bg-red-700 rotate-45">
            <div class="flex justify-center items-center absolute left-0 top-0 w-10 h-10 hover:-ml-1 hover:-mt-1 bg-gray-900 cursor-pointer">
              <a :href="facebook">
                <i class="fab fa-facebook-f fa-lg -rotate-45 text-gray-500"></i>
              </a>
            </div>
            <div class="flex justify-center items-center absolute top-0 right-0 w-10 h-10 hover:-mt-1 hover:-mr-1 bg-gray-900 cursor-pointer">
              <a :href="site">
                <i class="fas fa-globe fa-lg -rotate-45 text-gray-500"></i>
              </a>
            </div>
            <div class="flex justify-center items-center absolute right-0 bottom-0 w-10 h-10 hover:-mr-1 hover:-mb-1 bg-gray-900 cursor-pointer">
              <a :href="'mailto:' + mail">
                <i class="fas fa-envelope fa-lg -rotate-45 text-gray-500"></i>
              </a>
            </div>
            <div class="flex justify-center items-center absolute bottom-0 left-0 w-10 h-10 hover:-mb-1 hover:-ml-1 bg-gray-900 cursor-pointer">
              <a :href="location">
                <i class="fas fa-map-marker-alt fa-lg -rotate-45 text-gray-500"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- LINKS -->
        <div class="w-full md:w-1/3 p-5 text-center">
          <h1 class="uppercase">Links</h1>
          <ul>
            <li>
              <a href="https://wwww.vjf.be">Vlaamse Judo Federatie</a>
              <span class="text-sm">(Aansluitnr. 4033)</span>
            </li>
            <li>
              <a href="https://www.vjf.be/nl/ovl/oost-vlaanderen">Judo Oost-Vlaanderen</a>
            </i>
            <li>
              <a href="http://www.judoverenigingclinge.com/">Judovereniging Clinge</a>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.rotate-45 {
  transform: rotate(45deg);
}
.-rotate-45 {
  transform: rotate(-45deg);
}
</style>

<script>
import messages from './lang';

export default {
  i18n: messages,
  computed: {
    facebook() {
      return this.$store.state.site?.facebook;
    },
    mail() {
      return this.$store.state.site?.mail;
    },
    site() {
      return this.$store.state.site?.url;
    },
    location() {
      return this.$store.state.site?.location;
    }
  }
};
</script>
