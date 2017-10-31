<template>
    <v-layout row wrap>
        <v-flex xs12 sm8 offset-sm2>
            <NewsCard v-if="story" :story="story" :complete="true" />
        </v-flex>
    </v-layout>
</template>

<script>
    import NewsCard from '../components/NewsCard.vue';

    export default {
        components : {
            NewsCard
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
        }
    };
</script>
