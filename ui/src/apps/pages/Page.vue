<template>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-1 uk-width-2-3@m uk-width-4-5@xl">
                <slot></slot>
            </div>
            <div class="uk-width-1-1 uk-width-1-3@m uk-width-1-5@xl">
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
    </section>
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
