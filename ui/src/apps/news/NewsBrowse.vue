<template>
    <Page>
        <div slot="title" v-if="category" class="uk-light">
            <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
            <h3 class="uk-margin-remove">{{ category.name }}</h3>
            <p>
                {{ category.description }}
            </p>
        </div>
        <div slot="title" v-else-if="year && month" class="uk-light">
            <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
            <h3 class="uk-margin-remove">{{ $t('archive_title', { monthName : monthName, year : year }) }}</h3>
        </div>
        <div slot="content" class="uk-container uk-margin-top">
            <div v-if="$wait.is('news.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
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
        </div>
    </Page>
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import moment from 'moment';
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
                if (this.$route.params.category_id) {
                    return this.$store.getters['categoryModule/category'](this.$route.params.category_id);
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
                await vm.fetchData({
                      year : to.params.year,
                      month : to.params.month,
                      category : to.params.category_id,
                      featured : to.params.featured
                });
                next();
            });
        },
        watch : {
            '$route'(nv) {
                this.fetchData({
                    year : nv.params.year,
                    month : nv.params.month,
                    category : nv.params.category_id,
                    featured : nv.params.featured
                });
            }
        },
        methods : {
            fetchData(payload) {
                this.$store.dispatch('newsModule/browse', payload);
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
            readPage(offset) {
                this.fetchData({
                    offset : offset,
                    year : this.year,
                    month : this.month,
                    category : this.category_id,
                    featured : this.featured
                });
            }
        }
    };
</script>
