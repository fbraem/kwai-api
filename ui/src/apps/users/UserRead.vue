<template>
  <!-- eslint-disable max-len -->
  <div>
    <Spinner v-if="$wait.is('users.read')" />
    <div
      v-if="notAllowed"
      class="uk-alert-danger"
      uk-alert
    >
      {{ $t('not_allowed') }}
    </div>
    <div
      v-if="notFound"
      class="uk-alert-danger"
      uk-alert
    >
      {{ $t('user.not_found') }}
    </div>
    <div v-if="user"
      class="uk-container"
    >
      <div
        class="uk-flex uk-flex-middle"
        uk-grid
      >
        <div class="uk-width-1-1@s uk-width-1-3@m">
          <img :src="noAvatarImage" />
        </div>
        <div class="uk-width-1-1@s uk-width-2-3@m">
          <div uk-grid>
            <div class="uk-width-1-1">
              <h1>{{ user.name }}</h1>
            </div>
            <div class="uk-width-1-1">
              <a class="uk-button uk-button-default">
                <i class="fas fa-envelope uk-margin-small-right"></i>Mail
              </a>
              <a class="uk-button uk-button-default">
                <i class="fas fa-ban uk-margin-small-right"></i>Block
              </a>
              <router-link :to="{ name: 'users.rules', params: { id: user.id } }"
                class="uk-button uk-button-default"
              >
                <i class="fas fa-key uk-margin-small-right"></i>
                {{ $t('rules.title') }}
              </router-link>
            </div>
            <div class="uk-width-1-1">
              <div
                class="uk-grid uk-grid-divider uk-grid-medium uk-child-width-1-2"
                uk-grid
              >
                <div>
                  <span class="uk-text-small">
                    <i class="fas fa-calendar uk-margin-small-right uk-text-primary"></i>
                    {{ $t('member_since') }}
                  </span>
                  <div class="uk-text-large uk-margin-remove uk-text-primary">
                    {{ user.createdAtFormatted }}
                  </div>
                </div>
                <div>
                  <span class="uk-text-small">
                    <i class="fas fa-user uk-margin-small-right uk-text-success"></i>
                    {{ $t('last_login') }}
                  </span>
                  <div class="uk-text-large uk-margin-remove uk-text-success">
                    {{ user.lastLoginFormatted }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <section class="uk-section uk-section-default">
        <div class="uk-container uk-container-expand">
          <h4 class="uk-heading-line uk-text-bold">
            <span>
              {{ $t('news') }}
            </span>
          </h4>
          <p class="uk-text-meta">
            {{ $t('news_info') }}
          </p>
          <Spinner v-if="$wait.is('news.browse')" />
          <div v-if="hasStories">
            <table class="uk-table uk-table-striped">
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
            <Paginator
              v-if="storiesMeta"
              :count="storiesMeta.count"
              :limit="storiesMeta.limit"
              :offset="storiesMeta.offset"
              @page="loadStories"
            />
          </div>
          <h4 class="uk-heading-line uk-text-bold">
            <span>
              {{ $t('information') }}
            </span>
          </h4>
          <p class="uk-text-meta">
            {{ $t('information_info') }}
          </p>
          <Spinner v-if="$wait.is('pages.browse')" />
          <div v-if="hasPages">
            <table class="uk-table uk-table-striped">
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
            <Paginator
              v-if="pagesMeta"
              :count="pagesMeta.count"
              :limit="pagesMeta.limit"
              :offset="pagesMeta.offset"
              @page="loadPages"
            />
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

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
