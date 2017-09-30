<template>
    <site>
        <div slot="content">
            <h3><v-icon large>add</v-icon>&nbsp;Update News</h3>
            <news-form :story="story"></news-form>
        </div>
    </site>
</template>

<script>
    import NewsForm from './form.vue';
    import Site from '@/site/components/site.vue';
    export default {
        components : {
            NewsForm,
            Site
        },
        data() {
            return {};
        },
        mounted() {
            this.fetchData(this.$route.params.id);
        },
        computed : {
            story() {
                return this.$store.getters['newsModule/story'](this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData(to.params.id);
            next();
        },
        methods : {
            fetchData(id) {
                this.$store.dispatch('newsModule/read', {
                    id : id
                });
            }
        }
    };
</script>
