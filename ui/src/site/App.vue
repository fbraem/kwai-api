<template>
    <div>
        <v-container>
            <v-layout>
                <v-flex xs12 lg10 offset-lg1>
                    <v-card>
                        <v-card-media :src="require('./images/judokwaikemzeke.jpg')" height="500">
                            <v-container fill-height fluid>
                                <v-layout fill-height>
                                    <v-flex xs8 align-end flexbox>
                                        <div class="white--text" style="padding:5px;background-color:rgba(34,34,34,0.8)">
                                            <h3 class="headline">Judokwai Kemzeke</h3>
                                            <p>
                                                {{ $t("message.hello") }}
                                            </p>
                                            <p>
                                                {{ $t("message.info") }}
                                            </p>
                                        </div>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-media>
                    </v-card>
                    <v-container fluid class="button-container">
                        <v-layout row wrap>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="news.html" style="width:100%">
                                    <v-icon large color="red">fa-newspaper</v-icon>
                                    Nieuws
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="#" style="width:100%" disabled>
                                    <v-icon large color="red">fa-university</v-icon>
                                    Trainingen
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="#" style="width:100%" disabled>
                                    <v-icon large color="red">fa-trophy</v-icon>
                                    Tornooien
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="#" style="width:100%" disabled>
                                    <v-icon large color="red">fa-calendar</v-icon>
                                    Kalender
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="#" style="width:100%" disabled>
                                    <v-icon large color="red">fa-shopping-basket</v-icon>
                                    Materiaal
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="#" style="width:100%" disabled>
                                    <v-icon large color="red">fa-users</v-icon>
                                    Bestuur
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-layout>
        </v-container>
        <v-container grid-list-lg>
            <v-layout justify-space-around row wrap>
                <v-flex v-for="story in featuredNews" :key="story.id" xs12 sm6 md4>
                    <NewsCard :story="story" :complete="false"></NewsCard>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<style>
.button-container {
    border-radius: 2px;
    overflow: hidden;
    margin: 0;
}

.button-container .btn {
    background-color:hsla(0,0%,94%,.9);
    height:9rem;
    margin:0;
    border-radius:0
}

.button-container .btn .icon {
    font-size:2.5rem;
    margin-bottom:.25rem;
    color:#0279d7
}

.button-container .btn .btn__content {
    -webkit-box-orient:vertical;
    -webkit-box-direction:normal;
    -ms-flex-direction:column;
    flex-direction:column
}

@media screen and (max-width:959px) {
    .button-container .btn {
        height:6rem
    }
}
</style>

<script>
    import NewsCard from '@/apps/news/components/NewsCard.vue';

    export default {
        components : {
            NewsCard
        },
        data() {
            return {};
        },
        computed : {
            featuredNews() {
                return this.$store.getters['newsModule/stories'];
            }
        },
        created() {
            this.$store.dispatch('newsModule/browse', { featured : true });
        }
    };
</script>
