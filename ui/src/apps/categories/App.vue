<template>
    <router-view name="CategoryContent" />
</template>

<script>
    import categoryStore from './store';

    import messages from './lang';

    export default {
        i18n : messages,
        created() {
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
            //this.$store.dispatch('setSubTitle', this.$t('categories'));
        }
    };
</script>
