<template>
    <div class="uk-container uk-container-expand uk-margin-top uk-margin-bottom">
        <div>
            <div uk-grid>
                <div class="uk-width-1-1 uk-width-2-3@m">
                    <section class="uk-section uk-section-small uk-section-secondary uk-preserve-color">
                        <div class="uk-flex uk-flex-center uk-flex-middle uk-text-center">
                            <slot name="title">
                                <div class="uk-light">
                                    <h1 class="uk-margin-remove">{{ $t('page') }}</h1>
                                    <p>
                                        {{ $t('all_pages') }}
                                    </p>
                                </div>
                            </slot>
                        </div>
                    </section>
                    <slot name="content"></slot>
                </div>
                <div class="uk-width-1-1 uk-width-1-3@m">
                    <div class="uk-flex uk-flex-right">
                        <div>
                            <router-link :to="{ name : 'pages.browse' }" class="uk-icon-button">
                                <fa-icon name="home" />
                            </router-link>
                        </div>
                        <div v-if="$story.isAllowed('create')" class="uk-margin-small-left">
                            <router-link class="uk-icon-button" :to="{ 'name' : 'pages.create' }">
                                <fa-icon name="plus" />
                            </router-link>
                        </div>
                        <slot name="toolbar"></slot>
                    </div>
                    <div uk-grid class="uk-grid-small uk-child-width-1-1">
                        <div>
                            <h4 class="uk-heading-line uk-text-bold"><span>{{ $t('category') }}</span></h4>
                        </div>
                        <div>
                            <ul class="uk-list uk-list-divider">
                                <li v-for="(category, index) in categories">
                                    <router-link :to="{ name : 'pages.category', params : { category : category.id }}">
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
    </div>
</template>

<script>
    import 'vue-awesome/icons/home';
    import 'vue-awesome/icons/plus';

    import messages from './lang';

    import categoryStore from '@/stores/categories';

    export default {
        i18n : messages,
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            }
          },
        beforeCreate() {
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
        },
        created() {
            this.$store.dispatch('categoryModule/browse');
        }
    };
</script>
