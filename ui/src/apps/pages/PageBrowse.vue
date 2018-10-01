<template>
    <Page>
        <template slot="title">
            <div v-if="category" class="uk-width-1-1 uk-margin">
                <div class="uk-flex uk-flex-center uk-flex-middle uk-light uk-text-center">
                    <div v-if="category">
                        <h1 class="uk-margin-remove">{{ category.name }}</h1>
                        <h3 class="uk-margin-remove">{{ $t('page') }}</h3>
                        <p>
                            {{ category.description }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
        <div slot="content" class="uk-container uk-margin-top">
            <div v-if="$wait.is('pages.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div v-else>
                <div class="uk-child-width-1-1" uk-grid>
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
            </div>
            <div v-if="pageCount == 0">
                <div uk-alert>
                    {{ $t('no_pages') }}
                </div>
            </div>
        </div>
    </Page>
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import moment from 'moment';
    import Page from './Page.vue';
    import PageSummary from './components/PageSummary.vue';
    import Paginator from '@/components/Paginator.vue';

    import messages from './lang';

    import pageStore from '@/stores/pages';
    import categoryStore from '@/stores/categories';

    export default {
        i18n : messages,
        components : {
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
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                await vm.fetchData({
                    category : to.params.category
                });
                next();
            });
        },
        watch : {
            '$route'(nv) {
                this.fetchData({
                    category : nv.params.category
                });
            }
        },
        methods : {
            fetchData(payload) {
                this.$store.dispatch('pageModule/browse', payload);
            },
            readPage(offset) {
                console.log(offset);
            }
        }
    };
</script>
