<template>
  <div class="container mx-auto mt-6">
    <Spinner v-if="$wait.is('users.read')" />
    <Alert
      v-if="notAllowed"
      type="danger"
    >
      {{ $t('not_allowed') }}
    </Alert>
    <Alert
      v-if="notFound"
      type="danger"
    >
      {{ $t('not_found') }}
    </Alert>
    <div
      v-if="user"
      class="flex flex-wrap"
    >
      <div class="w-full sm:w-1/2 p-3">
        <img :src="noAvatarImage" />
      </div>
      <div class="w-full sm:w-1/2 flex flex-col p-3">
        <h1>{{ user.name }}</h1>
        <div class="flex flex-wrap justify-between p-3">
          <a class="default-button">
            <i class="fas fa-envelope"></i>Mail
          </a>
          <a class="default-button">
            <i class="fas fa-ban"></i>Block
          </a>
          <router-link
            :to="{ name: 'users.abilities', params: { id: user.id } }"
            class="default-button"
          >
            <i class="fas fa-key"></i>
            {{ $t('rules.title') }}
          </router-link>
        </div>
        <div class="flex flex-wrap justify-around p-3">
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
      <div class="w-full">
        <div>
          <h4 class="header-line">
            <span>
              {{ $t('news') }}
            </span>
          </h4>
          <p class="text-sm">
            {{ $t('news_info') }}
          </p>
          <Spinner v-if="$wait.is('news.browse')" />
          <div v-if="hasStories">
            <ul>
              <li v-for="story in stories" :key="story.id">
                <router-link
                  :to="{ name: 'news.story', params: { id : story.id } }"
                >
                  {{ story.content.title }}
                </router-link>
              </li>
            </ul>
            <div class="flex justify-center">
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
      <div class="w-full">
        <h4 class="header-line">
          <span>
            {{ $t('information') }}
          </span>
        </h4>
        <p class="text-sm">
          {{ $t('information_info') }}
        </p>
        <Spinner v-if="$wait.is('pages.browse')" />
        <div v-if="hasPages">
          <ul>
            <li
              v-for="page in pages"
              :key="page.id"
            >
              <router-link
                :to="{ name: 'pages.read', params: { id : page.id } }"
              >
                {{ page.content.title }}
              </router-link>
            </li>
          </ul>
          <div class="flex justify-center">
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
</template>

<script>
import messages from './lang';

import Paginator from '@/components/Paginator';
import Spinner from '@/components/Spinner';
import Alert from '@/components/Alert';

export default {
  components: {
    Paginator,
    Spinner,
    Alert
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
