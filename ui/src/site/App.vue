<template>
    <div>
        <v-container>
            <v-layout>
                <v-flex xs12 xl10 offset-xl1>
                    <v-card class="hidden-sm-and-up">
                        <v-container>
                            <v-layout row>
                                <v-flex xs7>
                                    <div>
                                        <p style="padding:5px">
                                            {{ $t("message.hello") }}
                                        </p>
                                        <p style="padding:5px;margin-bottom:0px;">
                                            {{ $t("message.info") }}
                                        </p>
                                    </div>
                                </v-flex>
                                <v-flex xs5>
                                    <v-card-media :src="require('./images/logo.jpg')" height="125" contain></v-card-media>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                    <v-card class="hidden-xs-only">
                        <v-card-media :src="require('./images/judokwaikemzeke.jpg')" height="500">
                            <v-container fill-height fluid>
                                <v-layout fill-height>
                                    <v-flex xs8 sm6 align-end flexbox>
                                        <div class="white--text" style="padding:5px;background-color:rgba(34,34,34,0.8);min-height:181px">
                                            <img :src="require('./images/logo.jpg')" style="width:171px;height:171px;float:left;margin-right:10px" />
                                            <p style="padding:5px">
                                                {{ $t("message.hello") }}
                                            </p>
                                            <p style="padding:5px;margin-bottom:0px;">
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
                                <v-btn :to="{ name : 'pages.category', params : { category_id : '2' } }" style="width:100%">
                                    <v-icon large color="red">fa-university</v-icon>
                                    Trainingen
                                </v-btn>
                            </v-flex>
                            <v-flex xs6 sm4 md2 px-0>
                                <v-btn :to="{ name : 'pages.category', params : { category_id : '3' } }" style="width:100%">
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
        <v-container fluid grid-list-xl class="ma-0 pa-0">
            <NewsBrowse :featured="true"></NewsBrowse>
        </v-container>
        <v-container fluid grid-list-xl>
            <v-layout row wrap>
                <v-flex xs12 sm6 xl3 d-flex>
                    <v-card color="blue-grey darken-2" class="white--text">
                        <v-container>
                            <v-layout row>
                                <v-flex xs8>
                                    <div>
                                        <div class="headline">
                                            Jeugdvriendelijke Judoclub
                                        </div>
                                        <div>
                                            Voor het derde jaar op rij verdient onze club
                                            goud bij de proclomatie van het jeugdjudofonds!
                                        </div>
                                    </div>
                                </v-flex>
                                <v-flex xs4>
                                    <v-card-media :src="require('./images/goud_jeugdsport_2017.jpg')" height="125" contain></v-card-media>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                </v-flex>
                <v-flex xs12 sm6 xl3 d-flex>
                    <v-card color="blue-grey darken-2" class="white--text">
                        <v-container>
                            <v-layout row>
                                <v-flex xs7>
                                    <div>
                                        <div class="headline">
                                            Locatie
                                        </div>
                                        <div>
                                            Wij trainen in de gevechtssportzaal van sportcentrum "De Sportstek" in Stekene,
                                            Nieuwstraat 60D.
                                        </div>
                                    </div>
                                </v-flex>
                                <v-flex xs5>
                                    <v-card-media :src="require('./images/sporthal.jpg')" height="125"></v-card-media>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                </v-flex>
                <v-flex xs12 sm6 xl3 d-flex>
                    <v-card color="blue-grey darken-2" class="white--text">
                        <v-container>
                            <v-layout row>
                                <v-flex xs7>
                                    <div>
                                        <div class="headline">
                                            Eens proberen?
                                        </div>
                                        <div>
                                            De Vlaamse Judo Federatie en Judokwai Kemzeke bieden u 4 gratis proeflessen aan.
                                        </div>
                                    </div>
                                </v-flex>
                                <v-flex xs5>
                                    <v-card-media :src="require('./images/kim_ono.png')" height="125" contain></v-card-media>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
                </v-flex>
                <v-flex xs12 sm6 xl3 d-flex>
                    <v-card color="blue-grey darken-2" class="white--text">
                        <v-container>
                            <v-layout row>
                                <v-flex xs7>
                                    <div>
                                        <div class="headline">
                                            Hartveilig
                                        </div>
                                        <div>
                                            Onze club is hartveilig. 10% van onze medewerkers zijn getraind in reanimatie.
                                        </div>
                                    </div>
                                </v-flex>
                                <v-flex xs5>
                                    <v-card-media :src="require('./images/hartveilig.jpg')" height="125" contain></v-card-media>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card>
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
