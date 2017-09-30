<template>
    <site>
        <div v-if="story" slot="content">
            <h2>{{ story.title }}</h2>
        </div>
    </site>
</template>

<script>
    import Site from '@/site/components/site.vue';

    export default {
        components : {
            Site
        },
        computed : {
            story() {
                return this.$store.getters['newsModule/story'](this.$route.params.id);
            }
        },
        mounted() {
          this.$store.dispatch('newsModule/read', { id : this.$route.params.id })
            .catch((error) => {
              console.log(error);
          });
        },
        methods : {
        }
    };
</script>
