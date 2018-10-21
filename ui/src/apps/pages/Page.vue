<template>
    <section class="uk-section uk-section-small uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-1 uk-width-2-3@m uk-width-4-5@xl">
                <slot></slot>
            </div>
            <div class="uk-width-1-1 uk-width-1-3@m uk-width-1-5@xl">
                <ListCategories :categories="categories" />
            </div>
        </div>
    </section>
</template>

<script>
    import 'vue-awesome/icons/home';
    import 'vue-awesome/icons/plus';

    import messages from './lang';

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
