<template>
    <div>
        <PageHeader :picture="picture">
            <div uk-grid class="uk-light">
                <div v-if="category" class="uk-width-1-1 uk-width-5-6@m">
                    <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
                    <h3 class="uk-margin-remove">{{ category.name }}</h3>
                    <p>
                        {{ category.description }}
                    </p>
                </div>
                <div v-else-if="year && month" class="uk-width-1-1 uk-width-5-6@m">
                    <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
                    <h3 class="uk-margin-remove">{{ $t('archive_title', { monthName : monthName, year : year }) }}</h3>
                </div>
                <div v-else class="uk-width-1-1 uk-width-5-6@m">
                    <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
                    <p>
                        {{ $t('all_news') }}
                    </p>
                </div>
                <div class="uk-width-1-1 uk-width-1-6@m">
                    <div class="uk-flex uk-flex-right">
                        <router-link v-if="$story.isAllowed('create')" class="uk-icon-button uk-link-reset" :to="{ name : 'news.create' }">
                            <i class="fas fa-plus"></i>
                        </router-link>
                    </div>
                </div>
            </div>
        </PageHeader>
        <Page>
            <div v-if="$wait.is('news.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class="fas fa-spinner fa-2x fa-spin"></i>
                </div>
            </div>
            <div v-else class="uk-child-width-1-1" uk-grid>
                <div v-if="storiesMeta">
                    <Paginator :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="readPage"></Paginator>
                </div>
                <div class="uk-child-width-1-1 uk-child-width-1-2@xl" uk-grid>
                    <NewsCard v-for="story in stories" :key="story.id" :story="story" @deleteStory="deleteStory"></NewsCard>
                </div>
                <div v-if="storiesMeta">
                    <Paginator :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="readPage"></Paginator>
                </div>
            </div>
            <div v-if="! $wait.is('news.browse') && newsCount == 0">
                <div uk-alert>
                    {{ $t('no_news') }}
                </div>
            </div>
            <AreYouSure id="delete-story" :yes="$t('delete')" :no="$t('cancel')" @sure="doDeleteStory">
                {{ $t('are_you_sure') }}
            </AreYouSure>
        </Page>
    </div>
</template>

<script>
    import moment from 'moment';
    import PageHeader from '@/site/components/PageHeader.vue';
    import Page from './Page.vue';
    import NewsCard from './components/NewsCard.vue';
    import Paginator from '@/components/Paginator.vue';
    import AreYouSure from '@/components/AreYouSure.vue';

    import UIkit from 'uikit';

    import messages from './lang';

    import newsStore from '@/stores/news';
    import categoryStore from '@/stores/categories';

    export default {
        i18n : messages,
        components : {
            PageHeader,
            Page,
            NewsCard,
            Paginator,
            AreYouSure
        },
        data() {
            return {
                showAreYouSure : false,
                storyToDelete : null
            };
        },
        computed : {
            stories() {
                return this.$store.getters['newsModule/stories'];
            },
            storiesMeta() {
                return this.$store.getters['newsModule/meta'];
            },
            newsCount() {
                if ( this.stories ) return this.stories.length;
                return -1;
            },
            category() {
                if (this.$route.params.category) {
                    return this.$store.getters['categoryModule/category'](this.$route.params.category);
                }
                return null;
            },
            picture() {
                if (this.category && this.category.images) {
                    return this.category.images.normal;
                }
                return null;
            },
            year() {
                return this.$route.params.year;
            },
            month() {
                return this.$route.params.month;
            },
            monthName() {
                return moment.months()[this.month -1];
            }
          },
        beforeCreate() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', newsStore);
            }
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                await vm.fetchData(to.params);
                next();
            });
        },
        watch : {
            async '$route'(nv) {
                await this.fetchData(nv.params);
            }
        },
        methods : {
            async fetchData(params) {
                if (params.category) {
                    await this.$store.dispatch('categoryModule/read', { id : params.category });
                }
                await this.$store.dispatch('newsModule/browse', {
                    year : params.year,
                    month : params.month,
                    category : params.category,
                    featured : params.featured
                });
            },
            deleteStory(story) {
                this.storyToDelete = story;
                var modal = UIkit.modal(document.getElementById('delete-story'));
                modal.show();
            },
            doDeleteStory() {
                this.$store.dispatch('newsModule/delete', {
                    story : this.storyToDelete
                });
            },
            async readPage(offset) {
                await this.$store.dispatch('newsModule/browse', {
                    offset : offset,
                    year : this.year,
                    month : this.month,
                    category : this.category ? this.category.id : null,
                    featured : this.featured
                });
            }
        }
    };
</script>
