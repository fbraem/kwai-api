<template>
    <div>
        <section class="uk-section uk-section-small uk-section-secondary">
            <div class="uk-container uk-container-expand">
                <div class="uk-flex uk-flex-center uk-flex-middle uk-light uk-text-center">
                    <div v-if="category">
                        <h1>{{ category.name }}</h1>
                        <p>
                            {{ category.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="uk-section uk-section-default uk-section-small">
            <div class="uk-container uk-container-expand">
                <div uk-grid class="uk-flex uk-margin">
                    <div class="uk-width-1-1">
                        <h4 class="uk-heading-line"><span>Nieuws</span></h4>
                        <div v-if="storyCount > 0" uk-slider="velocity: 5; autoplay-interval: 5000;autoplay: true; sets: true">
                            <div class="uk-position-relative">
                                <div class="uk-slider-container">
                                    <ul class="uk-slider-items uk-child-width-1-1@s uk-child-width-1-2@m uk-child-width-1-3@l uk-grid-medium uk-grid" uk-height-match="target: > li > div > .uk-card">
                                        <li v-for="story in stories">
                                            <NewsCard :story="story" :showCategory="false"></NewsCard>
                                        </li>
                                    </ul>
                                </div>
                                <div class="uk-hidden@m uk-light">
                                    <a class="uk-position-bottom-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                                    <a class="uk-position-bottom-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
                                </div>
                                <div class="uk-visible@m">
                                    <a class="uk-position-center-left-out uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                                    <a class="uk-position-center-right-out uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
                                </div>
                                <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                            </div>
                        </div>
                        <div v-else>
                            Geen nieuws ...
                        </div>
                    </div>
                    <div v-if="pageCount > 0" class="uk-width-1-1">
                        <h4 class="uk-heading-line"><span>Informatie</span></h4>
                        <div class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid-match" uk-grid="masonry: true">
                            <div v-for="page in pages" :page="page" :key="page.id">
                                <PageSummary :page="page"></PageSummary>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import messages from '../lang';

    import newsStore from '@/apps/news/store';
    import pageStore from '@/apps/pages/store';

    import 'vue-awesome/icons/ellipsis-h';

    import NewsCard from '@/apps/news/components/NewsCard.vue';
    import PageSummary from '@/apps/pages/components/PageSummary.vue';

    export default {
        i18n : messages,
        components : {
            NewsCard,
            PageSummary
        },
        computed : {
            category() {
                return this.$store.getters['categoryModule/category'](this.$route.params.id);
            },
            stories() {
                return this.$store.getters['newsModule/stories'];
            },
            storyCount() {
                if (this.stories) return this.stories.length;
                return 0;
            },
            pages() {
                return this.$store.getters['pageModule/pages'];
            },
            pageCount() {
                if (this.pages) return this.pages.length;
                return 0;
            },
            pageLink() {
                return {
                    name : 'pages.read',
                    params : {
                        id : this.page.id
                    }
                };
            }
        },
        created() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', newsStore);
            }
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', pageStore);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData();
        	next();
        },
        mounted() {
            this.fetchData();
        },
        methods : {
            fetchData() {
                this.$store.dispatch('categoryModule/read', { id : this.$route.params.id });
                this.$store.dispatch('newsModule/browse', { category : this.$route.params.id, featured : true });
                this.$store.dispatch('pageModule/browse', { category : this.$route.params.id });
            }
        }
    };
</script>
