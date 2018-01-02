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

    export default {
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
                this.$router.push('/category/' + id);
            }
        }
    };
</script>
