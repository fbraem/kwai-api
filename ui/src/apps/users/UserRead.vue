<template>
    <Page>
        <template slot="title">
            {{ $t('user_mgmt') }}
        </template>
        <template v-if="user" slot="header">
            <div class="uk-container uk-text-center">
                <div class="uk-flex uk-flex-middle" uk-grid>
                    <div class="uk-width-1-3">
                        <img :src="noAvatarImage" />
                    </div>
                    <div class="uk-width-2-3">
                        <h1>{{ user.name }}</h1>
                        <div uk-margin>
                            <a class="uk-button uk-button-default"><i class="fas fa-envelope uk-margin-small-right"></i>Mail</a>
                            <a class="uk-button uk-button-default"><i class="fas fa-ban uk-margin-small-right"></i>Block</a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template slot="content">
            <section v-if="user" class="uk-section uk-section-default uk-padding-remove">
                <div class="uk-container">
                    <div class="uk-grid uk-grid-small uk-flex uk-flex-middle" data-uk-grid>
                        <div class="uk-width-1-2">
                            <i class="fas fa-calendar uk-text-primary"></i>
                            <span class="uk-text-small uk-text-muted uk-text-bottom"> {{ $t('member_since') }}:</span><br />
                            <span class="uk-text-large uk-text-primary">{{ user.createdAtFormatted }}</span>
                        </div>
                        <div class="uk-width-1-2">
                            <i class="fas fa-user uk-text-success"></i>
                            <span class="uk-text-small uk-text-muted  uk-text-bottom"> {{ $t('last_login') }}:</span><br />
                            <span class="uk-text-large uk-text-success">{{ user.lastLoginFormatted }}</span>
                        </div>
                    </div>
                </div>
            </section>
            <section class="uk-section uk-section-default">
                <div class="uk-container uk-container-expand">
                    <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('news') }}</span></h4>
                    <p class="uk-text-meta">
                        {{ $t('news_info') }}
                    </p>
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
                    <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('information') }}</span></h4>
                    <p class="uk-text-meta">
                        {{ $t('information_info') }}
                    </p>
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
                </div>
            </section>
        </template>
    </Page>
</template>

<script>
    import messages from './lang';

    import userStore from '@/stores/users';
    import NewsStore from '@/stores/news';
    import PageStore from '@/stores/pages';

    import Paginator from '@/components/Paginator.vue';
    import Page from './Page.vue';

    export default {
        components : {
            Paginator,
            Page
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
            if (!this.$store.state.userModule) {
                this.$store.registerModule('userModule', userStore);
            }
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
