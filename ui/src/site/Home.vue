<template>
  <!-- eslint-disable max-len -->
  <div style="padding:20px;">
    <div class="icon-card-container kwai-hide-mobile">
      <div class="icon-card-item">
        <IconCard :to="{ name : 'news.browse' }" title="Nieuws">
          <template slot="icon">
            <i class="fas fa-newspaper fa-2x" style="color:#c61c18;"></i>
          </template>
          <template slot="text">
            Blijf op de hoogte over het reilen en zeilen van onze club.
          </template>
        </IconCard>
      </div>
      <div class="icon-card-item">
        <IconCard to="https://www.judokwaikemzeke.be/oud/kalender.htm" title="Kalender">
          <template slot="icon">
            <i class="fas fa-calendar fa-2x" style="color:#c61c18;"></i>
          </template>
          <template slot="text">
            Bekijk onze kalender voor activiteiten en tornooien
          </template>
        </IconCard>
      </div>
      <div v-for="category in categories" :key="category.id" class="icon-card-item">
        <CategoryCard :category="category" />
      </div>
    </div>
    <div class="kwai-hide-from-tablet">
      <div class="icon-card-container">
        <div class="icon-card-item">
          <IconCard :to="{ name : 'news.browse' }" title="Nieuws">
            <template slot="icon">
              <i class="fas fa-newspaper fa-2x" style="color:#c61c18;height:32px;"></i>
            </template>
            <template slot="text">
              Blijf op de hoogte over het reilen en zeilen van onze club.
            </template>
          </IconCard>
        </div>
        <div class="icon-card-item">
          <IconCard to="https://www.judokwaikemzeke.be/oud/kalender.htm" title="Kalender">
            <template slot="icon">
              <i class="fas fa-calendar fa-2x" style="color:#c61c18;height:32px;"></i>
            </template>
            <template slot="text">
              Bekijk onze kalender voor activiteiten en tornooien
            </template>
          </IconCard>
        </div>
      </div>
      <CategoryList v-if="categories" :categories="categories" />
    </div>
    <h4 class="kwai-header-line">
      Belangrijk Nieuws
    </h4>
    <div style="display: flex; justify-content: center;">
      <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories" />
    </div>
    <Spinner v-if="$wait.is('news.browse')"/>
    <div class="news-card-container">
      <div v-for="story in stories" :key="story.id" class="news-card-item">
        <NewsCard :story="story" @deleteStory="deleteStory"></NewsCard>
      </div>
    </div>
    <div style="display: flex; justify-content: center;">
      <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories" />
    </div>
    <router-link class="kwai-button" :to="{ name : 'news.browse' }">
      {{ $t('more_news') }}
    </router-link>
    <AreYouSure
      v-show="showAreYouSure"
      @close="close"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="doDeleteStory"
    >
      {{ $t('are_you_sure') }}
    </AreYouSure>
    <div class="message-card-container">
      <div class="message-card">
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
      <div class="message-card">
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
      <div class="message-card">
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
      <div class="message-card">
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
      <div class="message-card">
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
      <div class="message-card">
        <h3>Panathlon Verklaring</h3>
        <div style="display: flex; flex-direction: column;">
          <div>
            <p>Onze club onderschrijft de <a href="http://panathlonvlaanderen.be">Panathlon</a> verklaring.</p>
          </div>
          <div style="align-self: center; margin-top: 20px;padding: 15px; background-color: white;">
            <img :src="require('./images/panathlon.jpg')" style="height:125px" alt="">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import '@/site/scss/_mq.scss';

.message-card {
  background-color:#607d8b;
  padding: 20px;
}

.message-card h3 {
    color: white;
}

.message-card p {
  color: rgba(255, 255, 255, .7);
}

.message-card p a {
  color: white;
}

.message-card-container {
  margin-top: 20px;
  margin-left: 20%;
  margin-right: 20%;
  display: grid;
  grid-gap: 30px;

  @include mq($until: tablet) {
    grid-template-columns: auto;
    grid-template-rows: auto;
  }
  @include mq($from: wide) {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto;
  }
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
