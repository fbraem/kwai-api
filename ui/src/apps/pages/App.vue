<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div class="uk-width-2-3@m">
                    <div uk-grid class="uk-grid-small uk-child-width-1-1">
                        <div>
                            <h4 class="uk-heading-line">
                                <span>{{ $t('page') }}</span>
                            </h4>
                        </div>
                        <div>
                            <router-link :to="{ name : 'pages.browse' }" class="uk-icon-button">
                                <fa-icon name="home" />
                            </router-link>
                            <router-link v-if="$story.isAllowed('create')" :to="{ name : 'pages.create' }" class="uk-icon-button">
                                <fa-icon name="plus" />
                            </router-link>
                        </div>
                        <div>
                            <router-view name="PageContent" />
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-3@m">
                    <div uk-grid class="uk-grid-small uk-child-width-1-1">
                        <div>
                            <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('category') }}</span></h4>
                        </div>
                        <div>
                            <router-link :to="{ name : 'pages.browse' }" class="uk-icon-button">
                                <fa-icon name="home" />
                            </router-link>
                        </div>
                        <div>
                            <ul class="uk-list uk-list-divider">
                                <li v-for="(category, index) in categories">
                                    <router-link :to="{ name : 'pages.category', params : { category_id : category.id }}">
                                        {{ category.name }}
                                    </router-link>
                                    <div class="uk-text-meta">
                                        {{ category.description }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import 'vue-awesome/icons/home';
    import 'vue-awesome/icons/plus';

    import messages from './lang';

    import pageStore from './store';
    import categoryStore from '@/apps/categories/store';

    export default {
        i18n : messages,
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            }
          },
        created() {
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', pageStore);
            }
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
            this.$store.dispatch('setSubTitle', this.$t('pages'));
            this.$store.dispatch('categoryModule/browse');
        }
    };
</script>
