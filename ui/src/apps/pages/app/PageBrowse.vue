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
        </div>
        <div class="uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l uk-grid-match" uk-grid="masonry: true">
            <PageSummary v-for="page in pages" :page="page" :key="page.id"></PageSummary>
        </div>
        <div v-if="pageCount == 0">
            <div uk-alert>
                {{ $t('no_pages') }}
            </div>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import moment from 'moment';
    import PageSummary from './PageSummary.vue';
    //import Paginator from '@/components/Paginator.vue';

    import messages from '../lang';

    export default {
        i18n : messages,
        components : {
            PageSummary,
            //Paginator
        },
        props : [
            'category_id'
        ],
        data() {
            return {
            };
        },
        computed : {
            loading() {
                return this.$store.getters['pageModule/loading'];
            },
            pages() {
                return this.$store.getters['pageModule/pages'];
            },
            pageCount() {
                if ( this.pages ) return this.pages.length;
                return -1;
            },
            category() {
                if (this.category_id) {
                    return this.$store.getters['categoryModule/category'](this.category_id);
                }
                return null;
            }
          },
        created() {
            var categories = this.$store.getters['categoryModule/categories'];
            if (categories.length == 0) {
                this.$store.dispatch('categoryModule/browse');
            }
            this.fetchData({
                category : this.category_id
            });
        },
        watch : {
            '$route'() {
                this.fetchData({
                    category : this.$route.params.category_id
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
