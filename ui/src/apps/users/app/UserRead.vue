<template>
    <div class="uk-container">
        <div v-if="user" class="uk-card uk-card-default">
            <div class="uk-card-header">
                <div class="uk-grid-small uk-flex-center" uk-grid>
                    <div>
                        <img :src="noAvatarImage" />
                    </div>
                </div>
                <div class="uk-grid-small uk-flex-center" uk-grid>
                    <div>
                        {{ user.name }}
                    </div>
                </div>
            </div>
            <div class="uk-card-footer">
                <strong>{{ $t('last_login') }} :</strong> {{ user.lastLoginFormatted }}
            </div>
        </div>
        <section class="uk-section uk-section-small">
            <div class="uk-container uk-container-expand">
                <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('news') }}</span></h4>
                <p class="uk-text-meta">
                    {{ $t('news_info') }}
                </p>
            </div>
            <table v-if="hasStories" class="uk-table uk-table-striped">
                <thead>
                    <tr><th>{{ $t('title') }}</th></tr>
                </thead>
                <tbody>
                    <tr v-for="story in stories">
                        <td>
                            <router-link :to="{ name: 'news.story', params: { id : story.id } }">
                                {{ story.title }}
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories"></Paginator>
        </section>
        <section class="uk-section uk-section-small">
            <div class="uk-container uk-container-expand">
                <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('information') }}</span></h4>
                <p class="uk-text-meta">
                    {{ $t('information_info') }}
                </p>
            </div>
            <table v-if="hasPages" class="uk-table uk-table-striped">
                <thead>
                    <tr><th>{{ $t('title') }}</th></tr>
                </thead>
                <tbody>
                    <tr v-for="page in pages">
                        <td>
                            <router-link :to="{ name: 'pages.read', params: { id : page.id } }">
                                {{ page.title }}
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Paginator v-if="pagesMeta" :count="pagesMeta.count" :limit="pagesMeta.limit" :offset="pagesMeta.offset" @page="loadPages"></Paginator>
        </section>
    </div>
</template>

<script>
    import 'vue-awesome/icons/envelope';
    import 'vue-awesome/icons/ellipsis-h';

    import messages from '../lang';

    import NewsStore from '@/apps/news/store.js'
    import PageStore from '@/apps/pages/store.js'

    import Paginator from '@/components/Paginator.vue';

    export default {
        components : {
            Paginator
        },
        i18n : messages,
        computed : {
            user() {
                return this.$store.getters['userModule/user'](this.$route.params.id);
            },
            stories() {
                return this.$store.getters['newsModule/stories'];
            },
            storiesMeta() {
                return this.$store.getters['newsModule/meta'];
            },
            hasStories() {
                return this.stories && this.stories.length > 0;
            },
            pages() {
                return this.$store.getters['pageModule/pages'];
            },
            hasPages() {
                return this.pages && this.pages.length > 0;
            },
            pagesMeta() {
                return this.$store.getters['pageModule/meta'];
            },
            noAvatarImage() {
                return require('@/apps/users/images/no_avatar.png');
            },
            loading() {
                return this.$store.getters['userModule/loading'];
            }
        },
        created() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', NewsStore);
            }
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', PageStore);
            }
        },
        mounted() {
            this.fetchData();
        },
        watch : {
            '$route'() {
                this.fetchData();
            }
        },
        methods : {
            fetchData() {
                this.$store.dispatch('userModule/read', { id : this.$route.params.id })
                  .catch((error) => {
                    console.log(error);
                });
                this.loadStories(0);
                this.loadPages(0);
            },
            loadStories(offset) {
                this.$store.dispatch('newsModule/browse', {
                    user : this.$route.params.id,
                    offset : offset
                 }).catch((error) => {
                    console.log(error);
                });
            },
            loadPages(offset) {
                this.$store.dispatch('pageModule/browse', {
                    user : this.$route.params.id,
                    offset : offset
                 }).catch((error) => {
                    console.log(error);
                });
            },
            showNews(id) {
                this.$router.push({ name : 'news.story', params : { id : id }});
            },
            showPage(id) {
                this.$router.push({ name : 'pages.read', params : { id : id }});
            }
        }
    };
</script>
