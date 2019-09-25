<template>
  <!-- eslint-disable max-len -->
  <div class="page-container">
    <div style="grid-column: span 2;">
      <Spinner v-if="$wait.is('users.read')" />
      <div
        v-if="notAllowed"
        class="danger:kwai-alert"
      >
        {{ $t('not_allowed') }}
      </div>
      <div
        v-if="notFound"
        class="danger:kwai-alert"
      >
        {{ $t('user.not_found') }}
      </div>
      <div
        v-if="user"
        class="user-grid"
      >
        <div style="grid-area: user-avatar">
          <img :src="noAvatarImage" />
        </div>
        <div style="grid-area: user-info; display: flex; flex-direction: column;">
          <h1>{{ user.name }}</h1>
          <div style="display: flex; flex-wrap: wrap; justify-content: space-evenly">
            <a class="kwai-button">
              <i class="fas fa-envelope"></i>Mail
            </a>
            <a class="kwai-button">
              <i class="fas fa-ban"></i>Block
            </a>
            <router-link :to="{ name: 'users.abilities', params: { id: user.id } }"
              class="kwai-button"
            >
              <i class="fas fa-key"></i>
              {{ $t('rules.title') }}
            </router-link>
          </div>
          <div style="display: flex; flex-wrap: wrap; justify-content: space-around; margin-top: 20px;">
            <div>
              <span>
                <i class="fas fa-calendar"></i>
                {{ $t('member_since') }}
              </span>
              <div>
                {{ user.createdAtFormatted }}
              </div>
            </div>
            <div>
              <span>
                <i class="fas fa-user"></i>
                {{ $t('last_login') }}
              </span>
              <div>
                {{ user.lastLoginFormatted }}
              </div>
            </div>
          </div>
        </div>
        <div style="grid-area: user-news">
          <div>
            <h4 class="kwai-header-line">
              <span>
                {{ $t('news') }}
              </span>
            </h4>
            <p class="kwai-text-meta">
              {{ $t('news_info') }}
            </p>
            <Spinner v-if="$wait.is('news.browse')" />
            <div v-if="hasStories">
              <table class="kwai-table kwai-table-striped">
                <thead>
                  <tr>
                    <th>
                      {{ $t('title') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="story in stories" :key="story.id">
                    <td>
                      <router-link
                        :to="{ name: 'news.story', params: { id : story.id } }"
                      >
                        {{ story.content.title }}
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div style="display: flex; justify-content: center;">
                <Paginator
                  v-if="storiesMeta"
                  :count="storiesMeta.count"
                  :limit="storiesMeta.limit"
                  :offset="storiesMeta.offset"
                  @page="loadStories"
                />
              </div>
            </div>
          </div>
        </div>
        <div style="grid-area: user-pages">
          <h4 class="kwai-header-line">
            <span>
              {{ $t('information') }}
            </span>
          </h4>
          <p class="kwai-text-meta">
            {{ $t('information_info') }}
          </p>
          <Spinner v-if="$wait.is('pages.browse')" />
          <div v-if="hasPages">
            <table class="kwai-table kwai-table-striped">
              <thead>
                <tr>
                  <th>
                    {{ $t('title') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="page in pages"
                  :key="page.id"
                >
                  <td>
                    <router-link
                      :to="{ name: 'pages.read', params: { id : page.id } }"
                    >
                      {{ page.content.title }}
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
            <div style="display: flex; justify-content: center;">
              <Paginator
                v-if="pagesMeta"
                :count="pagesMeta.count"
                :limit="pagesMeta.limit"
                :offset="pagesMeta.offset"
                @page="loadPages"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import "@/site/scss/_mq.scss";

.user-grid {
  display: grid;
  grid-gap: 20px;

  @include mq($until: tablet) {
    grid-template-columns: 1fr;
    grid-template-rows: auto auto auto auto;
    grid-template-areas:
      "user-avatar"
      "user-info"
      "user-news"
      "user-pages"
    ;
  }
  @include mq($from: tablet) {
    grid-template-columns: auto 1fr;
    grid-template-rows: auto auto auto;
    grid-template-areas:
      "user-avatar user-info"
      "user-news user-news"
      "user-pages user-pages"
    ;
  }
}
</style>

<script>
import messages from './lang';

import Paginator from '@/components/Paginator';
import Spinner from '@/components/Spinner';

export default {
  components: {
    Paginator,
    Spinner
  },
  i18n: messages,
  computed: {
    user() {
      return this.$store.getters['user/user'](this.$route.params.id);
    },
    stories() {
      return this.$store.state.news.stories;
    },
    storiesMeta() {
      return this.$store.state.news.meta;
    },
    hasStories() {
      return this.stories && this.stories.length > 0;
    },
    pages() {
      return this.$store.state.page.pages;
    },
    hasPages() {
      return this.pages && this.pages.length > 0;
    },
    pagesMeta() {
      return this.$store.state.page.meta;
    },
    noAvatarImage() {
      return require('@/apps/users/images/no_avatar.png');
    },
    error() {
      return this.$store.state.user.error;
    },
    notAllowed() {
      return this.error && this.error.response.status === 401;
    },
    notFound() {
      return this.error && this.error.response.status === 404;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.id);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.id);
    next();
  },
  methods: {
    fetchData(id) {
      this.$store.dispatch('user/read', { id })
        .catch((error) => {
          console.log(error);
        });
      this.loadStories(0);
      this.loadPages(0);
    },
    loadStories(offset) {
      this.$store.dispatch('news/browse', {
        user: this.$route.params.id,
        offset
      }).catch((error) => {
        console.log(error);
      });
    },
    loadPages(offset) {
      this.$store.dispatch('page/browse', {
        user: this.$route.params.id,
        offset
      }).catch((error) => {
        console.log(error);
      });
    },
    showNews(id) {
      this.$router.push({
        name: 'news.story',
        params: { id }
      });
    },
    showPage(id) {
      this.$router.push({
        name: 'pages.read',
        params: { id }
      });
    }
  }
};
</script>
