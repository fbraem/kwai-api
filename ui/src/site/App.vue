<template>
    <div>
        <v-container>
            <v-layout>
                <v-flex xs12 xl10 offset-xl1>
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
                                <v-btn :to="{ name : 'news.browse' }" style="width:100%">
                                    <v-icon large color="red">fa-newspaper</v-icon>
                                    Nieuws
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="/oud/training/training.htm" style="width:100%">
                                    <v-icon large color="red">fa-university</v-icon>
                                    Trainingen
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="https://docs.google.com/spreadsheets/d/1nU2LaRgVjma7g9UHN_9ObUeOnUDqi55Ztb005p1FJW4/edit?usp=sharing" style="width:100%">
                                    <v-icon large color="red">fa-trophy</v-icon>
                                    Tornooien
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn href="/oud/kalender.htm" style="width:100%">
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
                                <v-btn href="oud/training/trainers.htm" style="width:100%">
                                    <v-icon large color="red">fa-users</v-icon>
                                    Bestuur
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-flex>
            </v-layout>
        </v-container>
        <NewsBrowse :featured="true"></NewsBrowse>
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
    import NewsBrowse from '@/apps/news/app/NewsBrowse.vue';

    export default {
        components : {
            NewsBrowse
        },
        data() {
            return {};
        },
        mounted() {
            this.$store.dispatch('setSubTitle', '');
        }
    };
</script>
