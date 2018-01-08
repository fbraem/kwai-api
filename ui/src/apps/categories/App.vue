<template>
    <v-container grid-list-xl ma-0 pa-0>
    </v-container>
</template>

<style>
.category-description {
    font-size: 12px;
    color: #999;
}
</style>

<script>
    import moment from 'moment';

    import messages from './lang/lang';

    export default {
        i18n : {
            messages
        },
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            }
          },
        created() {
            this.$store.dispatch('setSubTitle', this.$t('categories'));
            this.$store.dispatch('categoryModule/browse');
        },
        methods : {
            selectCategory(id) {
                this.$router.push({ name : 'category.read', params : { id : id }});
            }
        }
    };
</script>
