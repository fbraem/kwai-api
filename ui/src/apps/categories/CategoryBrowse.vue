<template>
    <div>
        <PageHeader>
            <div>
                <div uk-grid>
                    <div class="uk-width-expand">
                        <h1>{{ $t('categories') }}</h1>
                    </div>
                    <div class="uk-width-1-1 uk-width-1-6@m">
                        <div class="uk-flex uk-flex-right">
                            <div v-if="$category.isAllowed('create')">
                                <router-link class="uk-icon-button" :to="{ name : 'categories.create' }">
                                    <fa-icon name="plus" />
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section">
            <div class="uk-container">
                <div class="uk-child-width-1-1 uk-child-width-1-2@l" uk-grid>
                    <div v-for="category in categories" :key="category.id">
                        <div class="uk-card uk-card-body uk-card-default uk-card-small uk-card-hover">
                            <router-link :to="{ name: 'categories.read', params: { id : category.id } }" class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent"></router-link>
                            <h3 class="uk-card-title">
                                {{ category.name }}
                            </h3>
                            <p v-html="category.description">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import PageHeader from '@/site/components/PageHeader.vue';

    import 'vue-awesome/icons/plus';

    import messages from './lang';

    import categoryStore from '@/stores/categories';

    export default {
        components : {
            PageHeader
        },
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
        mounted() {
            this.fetchData();
        },
        methods : {
            fetchData() {
                this.$store.dispatch('categoryModule/browse');
            }
        }
    };
</script>
