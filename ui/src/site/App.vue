<template>
  <!-- eslint-disable max-len -->
  <div>
    <PageHeader :picture="require('./images/judokwaikemzeke.jpg')">
      <div uk-grid class="uk-flex uk-flex-center">
        <div class="uk-width-1-1">
          <img class="uk-align-center"
            :src="require('./images/logo2.png')"
            style="width:121px;height:121px;" />
        </div>
        <div class="uk-width-1-1">
          <h4 class="uk-margin-remove-top uk-text-center">Judokwai Kemzeke</h4>
          {{ $t("message.hello") }}
          <br />
          {{ $t("message.info") }}
        </div>
      </div>
    </PageHeader>
    <section class="uk-section uk-section-small uk-section-muted uk-visible@m">
      <div class="uk-container uk-container-large">
        <div class="uk-margin uk-text-center uk-child-width-1-2@m uk-child-width-1-4@l uk-grid-medium uk-grid-match uk-flex uk-flex-center" uk-height-match=".uk-card" uk-grid>
          <div>
            <Card :to="{ name : 'news.browse' }" title="Nieuws">
              <template slot="icon">
                <i class="fas fa-newspaper fa-3x" style="color:#c61c18;height:32px;"></i>
              </template>
              <template slot="description">
                Blijf op de hoogte over het reilen en zeilen van onze club.
              </template>
            </Card>
          </div>
          <div>
            <Card to="https://www.judokwaikemzeke.be/oud/kalender.htm" title="Kalender">
              <template slot="icon">
                <i class="fas fa-calendar fa-3x" style="color:#c61c18;height:32px;"></i>
              </template>
              <template slot="description">
                Bekijk onze kalender voor trainingen, activiteiten en tornooien
              </template>
            </Card>
          </div>
          <div v-for="category in categories" :key="category.id">
            <CategoryCard :category="category" />
          </div>
        </div>
      </div>
    </section>
    <section class="uk-section uk-section-small uk-section-muted uk-hidden@m">
      <div class="uk-container uk-container-large">
        <div class="uk-margin uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-4@l uk-grid-medium uk-grid-match uk-flex uk-flex-center" uk-height-match=".uk-card" uk-grid>
          <div class="uk-text-center">
            <Card :to="{ name : 'news.browse' }" title="Nieuws">
              <template slot="icon">
                <i class="fas fa-newspaper fa-3x" style="color:#c61c18;height:32px;"></i>
              </template>
              <template slot="description">
                Blijf op de hoogte over het reilen en zeilen van onze club.
              </template>
            </Card>
          </div>
          <div class="uk-text-center">
            <Card to="https://www.judokwaikemzeke.be/oud/kalender.htm" title="Kalender">
              <template slot="icon">
                <i class="fas fa-calendar fa-3x" style="color:#c61c18;height:32px;"></i>
              </template>
              <template slot="description">
                Bekijk onze kalender voor trainingen, activiteiten en tornooien
              </template>
            </Card>
          </div>
          <div>
            <div v-if="$wait.is('categories.browse')" class="uk-flex-center">
              <div class="uk-text-center">
                <i class="fas fa-spinner fa-2x fa-spin"></i>
              </div>
            </div>
            <CategoryList v-if="categories" :categories="categories" />
          </div>
        </div>
      </div>
    </section>
    <section class="uk-section uk-section-small">
      <div class="uk-container uk-container-large">
        <div class="uk-child-width-1-1" uk-grid>
          <div v-if="$wait.is('news.browse')" class="uk-flex-center">
            <div class="uk-text-center">
              <i class="fas fa-spinner fa-2x fa-spin"></i>
            </div>
          </div>
          <div v-else class="uk-child-width-1-1" uk-grid>
            <h4 class="uk-heading-line uk-text-bold" id="newsgrid">
              <span>Belangrijk Nieuws</span>
            </h4>
            <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories"></Paginator>
            <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid" uk-grid>
              <NewsCard v-for="story in stories" :story="story" :key="story.id" @deleteStory="deleteStory"></NewsCard>
            </div>
            <div style="clear:both"></div>
            <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories"></Paginator>
            <div>
              <router-link class="uk-button uk-button-default" :to="{ name : 'news.browse' }">
                {{ $t('more_news') }}
              </router-link>
            </div>
            <AreYouSure id="delete-story" :yes="$t('delete')" :no="$t('cancel')" @sure="doDeleteStory">
              {{ $t('are_you_sure') }}
            </AreYouSure>
          </div>
        </div>
      </div>
    </section>
    <section class="uk-section uk-section-small">
      <div class="uk-container">
        <div class="uk-child-width-1-2@m uk-grid-small uk-grid-match" uk-grid>
          <div>
            <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
              <h3 class="uk-card-title">Jeugdvriendelijke Judoclub</h3>
              <div class="uk-flex-center" uk-grid>
                <div>
                  <p>Voor het vierde jaar op rij verdient onze club goud bij de proclomatie van het jeugdjudofonds!</p>
                </div>
                <div>
                  <img :src="require('./images/goud_jeugdsport_2018.png')" style="height:125px" alt="">
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
              <h3 class="uk-card-title" style="color:white">Locatie</h3>
              <div class="uk-flex-center" uk-grid>
                <div>
                  <p>Wij trainen in de gevechtssportzaal van sportcentrum
                    <strong>"De Sportstek"</strong> in Stekene, Nieuwstraat 60D.</p>
                  </div>
                  <div>
                    <img :src="require('./images/sporthal.jpg')" style="height:125px" alt="">
                  </div>
                </div>
              </div>
            </div>
            <div>
              <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                <h3 class="uk-card-title">Eens proberen?</h3>
                <div class="uk-flex-center" uk-grid>
                  <div>
                    <p>De <a href="https://www.vjf.be">Vlaamse Judo Federatie</a> en Judokwai Kemzeke bieden u 4 gratis proeflessen aan.</p>
                  </div>
                  <div>
                    <img :src="require('./images/kim_ono.png')" style="height:125px;" alt="">
                  </div>
                </div>
              </div>
            </div>
            <div>
              <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                <h3 class="uk-card-title">Hartveilig</h3>
                <div class="uk-flex-center" uk-grid>
                  <div>
                    <p>Onze club is hartveilig. 10% van onze medewerkers zijn getraind in reanimatie.</p>
                  </div>
                  <div>
                    <img :src="require('./images/hartveilig.jpg')" alt="">
                  </div>
                </div>
              </div>
            </div>
            <div>
              <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                <h3 class="uk-card-title">Gezond sporten</h3>
                <div class="uk-flex" uk-grid>
                  <div class="uk-flex-left">
                    <p>Onze club draagt <a href="https://www.vjf.be/nl/aanvulling-en-aanpassing-vjf-website-gezond-en-ethisch-sporten">Gezond Sporten</a> hoog in het het vaandel.</p>
                  </div>
                  <div class="uk-align-center" style="background-color:white;padding:10px">
                    <img :src="require('./images/gezond.jpg')" style="height:125px" alt="">
                  </div>
                </div>
              </div>
            </div>
            <div>
              <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                <h3 class="uk-card-title">Panathlon Verklaring</h3>
                <div class="uk-flex-center" uk-grid>
                  <div class="uk-width-1-1">
                    <p>Onze club onderschrijft de <a href="http://panathlonvlaanderen.be">Panathlon</a> verklaring.</p>
                  </div>
                  <div style="background-color:white;padding:10px">
                    <img :src="require('./images/panathlon.jpg')" style="height:125px" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
</template>

<style>
.message-card {
    background-color:#607d8b;
}
.message-card h3 {
    color: white!important;
}
</style>

<script>
import NewsCard from '@/apps/news/components/NewsCard.vue';
import Paginator from '@/components/Paginator.vue';
import Card from './Card.vue';
import PageHeader from './components/PageHeader.vue';
import AreYouSure from '@/components/AreYouSure.vue';
import CategoryCard from '@/apps/categories/components/Card.vue';
import CategoryList from '@/apps/categories/components/List.vue';

import newsStore from '@/stores/news';
import categoryStore from '@/stores/categories';
import registerModule from '@/stores/mixin';

import UIkit from 'uikit';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    NewsCard,
    Paginator,
    Card,
    PageHeader,
    AreYouSure,
    CategoryCard,
    CategoryList
  },
  mixins: [
    registerModule(
      {
        news: newsStore
      },
      {
        category: categoryStore
      }
    ),
  ],
  data() {
    return {
      storyToDelete: null
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
      var modal = UIkit.modal(document.getElementById('delete-story'));
      modal.show();
    },
    doDeleteStory() {
      this.$store.dispatch('news/delete', {
        story: this.storyToDelete
      });
    },
  }
};
</script>
