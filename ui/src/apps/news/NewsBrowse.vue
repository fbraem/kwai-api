<template>
    <div class="uk-container">
        <div uk-grid class="uk-flex uk-margin">
            <div v-if="category" class="uk-width-1-1">
                <section class="uk-section uk-section-small uk-section-secondary">
                    <div class="uk-container uk-container-expand">
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-light uk-text-center">
                            <div v-if="category">
                                <h1 class="uk-margin-remove">{{ category.name }}</h1>
                                <h3 class="uk-margin-remove">{{ $t('news') }}</h3>
                                <p>
                                    {{ category.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div v-else-if="year && month" class="uk-width-1-1">
                <section class="uk-section uk-section-small uk-section-secondary">
                    <div class="uk-container uk-container-expand">
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-light uk-text-center">
                            <div>
                                <h1 class="uk-margin-remove">{{ $t('archive_title', { monthName : monthName, year : year }) }}</h1>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div v-else class="uk-width-1-1">
                <section class="uk-section uk-section-small uk-section-secondary">
                    <div class="uk-container uk-container-expand">
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-light uk-text-center">
                            <div>
                                <h1 class="uk-margin-remove">{{ $t('news') }}</h1>
                                <p>
                                    {{ $t('all_news') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="uk-child-width-1-1" uk-grid>
            <div v-if="loading" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else>
                <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="readPage"></Paginator>
                <NewsCard v-for="story in stories" :story="story" :key="story.id"></NewsCard>
                <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="readPage"></Paginator>
            </div>
        </div>
        <div v-if="! loading && newsCount == 0">
            <div uk-alert>
                {{ $t('no_news') }}
            </div>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import moment from 'moment';
    import NewsCard from './components/NewsCard.vue';
    import Paginator from '@/components/Paginator.vue';

    import messages from './lang';

    export default {
        i18n : messages,
        components : {
            NewsCard,
            Paginator
        },
        props : [
            'year',
            'month',
            'category_id'
        ],
        data() {
            return {
                showAreYouSure : false,
                storyToDelete : null
            };
        },
        computed : {
            loading() {
                return this.$store.getters['newsModule/loading'];
            },
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
                if (this.category_id) {
                    return this.$store.getters['categoryModule/category'](this.category_id);
                }
                return null;
            },
            monthName() {
                return moment.months()[this.month -1];
            }
          },
        mounted() {
            var categories = this.$store.getters['categoryModule/categories'];
            if (categories.length == 0) {
                this.$store.dispatch('categoryModule/browse');
            }
            this.fetchData({
                year : this.year,
                month : this.month,
                category : this.category_id,
                featured : this.featured
            });
        },
        watch : {
            '$route'() {
                this.fetchData({
                    year : this.$route.params.year,
                    month : this.$route.params.month,
                    category : this.$route.params.category_id
                });
            }
        },
        methods : {
            fetchData(payload) {
                this.$store.dispatch('newsModule/browse', payload);
            },
            areYouSure(id) {
                this.showAreYouSure = true;
                this.storyToDelete = id;
            },
            deleteStory() {
                this.showAreYouSure = false;
                this.$store.dispatch('newsModule/delete', {
                    id : this.storyToDelete
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
