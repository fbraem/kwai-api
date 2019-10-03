<template>
  <!-- eslint-disable max-len -->
  <div class="p-4">
    <div class="hidden sm:block container mx-auto bg-gray-100">
      <div class="flex flex-row flex-wrap justify-center p-4">
        <div class="w-full sm:w-1/2 md:w-1/4">
          <IconCard :to="{ name : 'news.browse' }" title="Nieuws">
            <template slot="icon">
              <span class="text-red-700">
                <i class="fas fa-newspaper fa-2x"></i>
              </span>
            </template>
            <template slot="text">
              Blijf op de hoogte over het reilen en zeilen van onze club.
            </template>
          </IconCard>
        </div>
        <div class="w-full sm:w-1/2 md:w-1/4">
          <IconCard
            to="https://www.judokwaikemzeke.be/oud/kalender.htm"
            title="Kalender"
          >
            <template slot="icon">
              <span class="text-red-700">
                <i class="fas fa-calendar fa-2x"></i>
              </span>
            </template>
            <template slot="text">
              Bekijk onze kalender voor activiteiten en tornooien
            </template>
          </IconCard>
        </div>
        <div
          v-for="category in categories"
          :key="category.id"
          class="w-full sm:w-1/2 md:w-1/4"
        >
          <CategoryCard :category="category" />
        </div>
      </div>
    </div>
    <div class="block sm:hidden">
      <div class="flex flex-wrap justify-center">
        <IconCard
          :to="{ name : 'news.browse' }"
          title="Nieuws"
        >
          <template slot="icon">
            <span class="text-red-700">
              <i
                class="fas fa-newspaper fa-2x"
                style="height:32px;"
              >
              </i>
            </span>
          </template>
          <template slot="text">
            Blijf op de hoogte over het reilen en zeilen van onze club.
          </template>
        </IconCard>
        <IconCard
          to="https://www.judokwaikemzeke.be/oud/kalender.htm"
          title="Kalender"
        >
          <template slot="icon">
            <span class="text-red-700">
              <i
                class="fas fa-calendar fa-2x"
                style="height:32px;"
              >
              </i>
            </span>
          </template>
          <template slot="text">
            Bekijk onze kalender voor activiteiten en tornooien
          </template>
        </IconCard>
      </div>
      <CategoryList
        v-if="categories"
        :categories="categories"
      />
    </div>
    <h4 class="header-line">
      Belangrijk Nieuws
    </h4>
    <div class="flex justify-center">
      <Paginator
        v-if="storiesMeta"
        :count="storiesMeta.count"
        :limit="storiesMeta.limit"
        :offset="storiesMeta.offset"
        @page="loadStories"
      />
    </div>
    <Spinner v-if="$wait.is('news.browse')"/>
    <div class="flex flex-wrap justify-center mb-4">
      <div
        v-for="story in stories"
        :key="story.id"
        class="p-2 w-full lg:w-1/3 xl:w-1/4"
      >
        <NewsCard
          :story="story"
          @deleteStory="deleteStory"
        />
      </div>
    </div>
    <div class="flex justify-center">
      <Paginator
        v-if="storiesMeta"
        :count="storiesMeta.count"
        :limit="storiesMeta.limit"
        :offset="storiesMeta.offset"
        @page="loadStories"
      />
    </div>
    <div class="block mb-4">
      <router-link
        class="red-button"
        :to="{ name : 'news.browse' }"
      >
        {{ $t('more_news') }}
      </router-link>
    </div>
    <div class="container mx-auto message-card-container">
      <div class="message-card">
        <div class="message-card-content">
          <h3>Jeugdvriendelijke Judoclub</h3>
          <div style="display: flex; flex-direction: column;">
            <div>
              <p>Voor het vijfde jaar op rij verdient onze club goud bij de proclomatie van het jeugdjudofonds!</p>
            </div>
            <div style="align-self: center; padding: 15px; margin-top: 20px; background-color: white;">
              <img :src="require('./images/goud_jeugdsport_2019.jpg')" style="height:125px" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="message-card">
        <div class="message-card-content">
          <h3>Locatie</h3>
          <div style="display: flex; flex-direction: column;">
            <div>
              <p>Wij trainen in de gevechtssportzaal van sportcentrum
              <strong>"De Sportstek"</strong> in Stekene, Nieuwstraat 60D.</p>
            </div>
            <div style="align-self: center; margin-top: 20px;">
              <img :src="require('./images/sporthal.jpg')" style="height:125px" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="message-card">
        <div class="message-card-content">
          <h3>Eens proberen?</h3>
          <div style="display: flex; flex-direction: column;">
            <div>
              <p>De <a href="https://www.vjf.be">Vlaamse Judo Federatie</a> en Judokwai Kemzeke bieden u 4 gratis proeflessen aan.</p>
            </div>
            <div style="align-self: center; margin-top: 20px;">
              <img :src="require('./images/kim_ono.png')" style="height:125px;" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="message-card">
        <div class="message-card-content">
          <h3>Hartveilig</h3>
          <div style="display: flex; flex-direction: column;">
            <div>
              <p>Onze club is hartveilig. 10% van onze medewerkers zijn getraind in reanimatie.</p>
            </div>
            <div style="align-self: center; margin-top: 20px; padding: 15px; background-color: white;">
              <img :src="require('./images/hartveilig.jpg')" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="message-card">
        <div class="message-card-content">
          <h3>Gezond sporten</h3>
          <div style="display: flex; flex-direction: column;">
            <div>
              <p>Onze club draagt <a href="https://www.vjf.be/nl/aanvulling-en-aanpassing-vjf-website-gezond-en-ethisch-sporten">Gezond Sporten</a> hoog in het het vaandel.</p>
            </div>
            <div style="align-self: center; margin-top: 20px; padding: 15px; background-color: white;">
              <img :src="require('./images/gezond.jpg')" style="height:125px" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="message-card">
        <div class="message-card-content">
          <h3>Panathlon Verklaring</h3>
          <div style="display: flex; flex-direction: column;">
            <div>
              <p>
                Onze club onderschrijft de
                <a class="text-white font-bold" href="http://panathlonvlaanderen.be">Panathlon</a> verklaring.</p>
            </div>
            <div style="align-self: center; margin-top: 20px;padding: 15px; background-color: white;">
              <img :src="require('./images/panathlon.jpg')" style="height:125px" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
    <AreYouSure
      v-show="showAreYouSure"
      @close="close"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="doDeleteStory"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
  </div>
</template>

<style scoped>
.message-card-container {
  display: grid;
  grid-gap: 30px;

  grid-template-columns: 1fr;
  grid-auto-rows: 1fr;
}
@screen md {
  .message-card-container {
    grid-template-columns: 1fr 1fr;
  }
}
.message-card {
  @apply w-full text-white;
}
.message-card-content {
  @apply bg-tatami p-5 h-full;
}
</style>

<script>
import NewsCard from '@/apps/news/components/NewsCard.vue';
import Paginator from '@/components/Paginator.vue';
import Spinner from '@/components/Spinner.vue';
import IconCard from '@/components/IconCard.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import CategoryCard from '@/apps/categories/components/CategoryCard.vue';
import CategoryList from '@/apps/categories/components/CategoryList.vue';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    NewsCard,
    Paginator,
    Spinner,
    IconCard,
    AreYouSure,
    CategoryCard,
    CategoryList
  },
  data() {
    return {
      storyToDelete: null,
      showAreYouSure: false
    };
  },
  computed: {
    stories() {
      return this.$store.state.news.stories;
    },
    storiesMeta() {
      return this.$store.state.news.meta;
    },
    categories() {
      return this.$store.state.category.categories;
    }
  },
  created() {
    this.$store.dispatch('setSubTitle', '');
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
    async fetchData() {
      await this.loadStories(0);
      this.$store.dispatch('category/browse');
    },
    async loadStories(offset) {
      try {
        await this.$store.dispatch('news/browse', {
          offset: offset, featured: true
        });
      } catch (error) {
        console.log(error);
      }
    },
    deleteStory(story) {
      this.storyToDelete = story;
      this.showAreYouSure = true;
    },
    doDeleteStory() {
      this.showAreYouSure = false;
      this.$store.dispatch('news/delete', {
        story: this.storyToDelete
      });
    },
    close() {
      this.showAreYouSure = false;
    }
  }
};
</script>
