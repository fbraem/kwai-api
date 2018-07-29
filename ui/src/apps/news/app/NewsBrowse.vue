<template>
    <div class="uk-container">
        <div v-if="loading" class="uk-flex-center" uk-grid>
            <div class="uk-text-center">
                <fa-icon name="spinner" scale="2" spin />
            </div>
        </div>
        <div v-else uk-grid class="uk-flex uk-margin">
            <div v-if="category" class="uk-width-1-1">
                <div class="uk-tile uk-tile-muted uk-padding-small" style="border:1px solid rgba(0,0,0,0.075)">
                    <h3>{{ category.name }}</h3>
                    <div class="uk-text-meta">
                        {{ category.description }}
                    </div>
                </div>
            </div>
            <div v-if="year && month" class="uk-width-1-1">
                <div class="uk-tile uk-tile-muted uk-padding-small" style="border:1px solid rgba(0,0,0,0.075)">
                    <h3>{{ $t('archive_title', { monthName : monthName, year : year }) }}</h3>
                </div>
            </div>
        </div>
        <div class="uk-child-width-1-1 uk-child-width-1-2@l uk-grid" uk-grid="masonry: true">
            <NewsCard v-for="story in stories" :story="story" :key="story.id"></NewsCard>
        </div>
        <div v-if="newsCount == 0">
            <div uk-alert>
                {{ $t('no_news') }}
            </div>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import moment from 'moment';
    import NewsCard from '../components/NewsCard.vue';
    import Paginator from '@/components/Paginator.vue';

    import messages from '../lang';

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
                console.log(offset);
            }
        }
    };
</script>
