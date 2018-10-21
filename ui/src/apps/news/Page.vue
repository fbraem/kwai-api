<template>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
        <div>
            <div uk-grid>
                <div class="uk-width-1-1 uk-width-2-3@m uk-width-4-5@xl">
                    <slot></slot>
                </div>
                <div class="uk-width-1-1 uk-width-1-3@m uk-width-1-5@xl">
                    <ListCategories :categories="categories" />
                    <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('archive') }}</span></h4>
                    <template v-for="(months, year) in archive">
                        <h5>{{ year }}</h5>
                        <ul class="uk-list">
                            <li v-for="(month) in months" :key="month.month">
                                <router-link :to="{ name : 'news.archive', params : { year : year, month : month.month }}">
                                    {{ month.monthName }} {{ year }} <span class="uk-badge uk-float-right">{{ month.count }}</span>
                                </router-link>
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import 'vue-awesome/icons/home';
    import 'vue-awesome/icons/plus';

    import messages from './lang';

    import newsStore from '@/stores/news';
    import categoryStore from '@/stores/categories';

    import ListCategories from '@/apps/categories/components/List.vue';

    export default {
        i18n : messages,
        components : {
            ListCategories
        },
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            },
            archive() {
                return this.$store.getters['newsModule/archive'];
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
        created() {
            this.$store.dispatch('categoryModule/browse');
            this.$store.dispatch('newsModule/loadArchive');
        }
    };
</script>
