<template>
    <div>
        <PageHeader :picture="picture">
            <div class="uk-light" uk-grid>
                <div class="uk-width-1-1 uk-width-5-6@m">
                    <div v-if="category">
                        <h1 class="uk-margin-remove">{{ category.name }}</h1>
                        <h3 class="uk-margin-remove">{{ $t('page') }}</h3>
                        <p>
                            {{ category.description }}
                        </p>
                    </div>
                    <div v-else>
                        <h1 class="uk-margin-remove">{{ $t('page') }}</h1>
                        <p>
                            {{ $t('all_pages') }}
                        </p>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-1-6@m">
                    <div class="uk-flex uk-flex-right">
                        <router-link v-if="$page.isAllowed('create')" class="uk-icon-button" :to="{ name : 'pages.create' }">
                            <i class="fas fa-plus"></i>
                        </router-link>
                    </div>
                </div>
            </div>
        </PageHeader>
        <Page>
            <div v-if="$wait.is('pages.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class="fas fa-spinner fa-2x fa-spin"></i>
                </div>
            </div>
            <div v-else class="uk-child-width-1-1" uk-grid>
                <div v-if="pagesMeta">
                    <Paginator :count="pagesMeta.count" :limit="pagesMeta.limit" :offset="pagesMeta.offset" @page="readPage"></Paginator>
                </div>
                <div class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid-match" uk-grid>
                    <PageSummary v-for="page in pages" :page="page" :key="page.id"></PageSummary>
                </div>
                <div v-if="pagesMeta">
                    <Paginator :count="pagesMeta.count" :limit="pagesMeta.limit" :offset="pagesMeta.offset" @page="readPage"></Paginator>
                </div>
            </div>
        </Page>
    </div>
</template>

<script>
    import moment from 'moment';
    import PageHeader from '@/site/components/PageHeader.vue';
    import Page from './Page.vue';
    import PageSummary from './components/PageSummary.vue';
    import Paginator from '@/components/Paginator.vue';

    import messages from './lang';

    import pageStore from '@/stores/pages';
    import categoryStore from '@/stores/categories';

    export default {
        i18n : messages,
        components : {
            PageHeader,
            Page,
            PageSummary,
            Paginator
        },
        data() {
            return {
            };
        },
        computed : {
            pages() {
                return this.$store.getters['pageModule/pages'];
            },
            pageCount() {
                if ( this.pages ) return this.pages.length;
                return -1;
            },
            pagesMeta() {
                return null;
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
            }
        },
        beforeCreate() {
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', pageStore);
            }
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
        },
        async created() {
            await this.fetchData(this.$route.params);
        },
        async beforeRouteUpdate(to, from, next) {
            await this.fetchData(to.params);
            next();
        },
        methods : {
            fetchData(params) {
                this.$store.dispatch('pageModule/browse', {
                    category : params.category
                });
            },
            readPage(offset) {
                console.log(offset);
            }
        }
    };
</script>
