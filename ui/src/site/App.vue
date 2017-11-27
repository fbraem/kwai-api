<template>
    <v-layout>
        <v-flex xs12 lg8 offset-lg2>
            <v-card>
                <v-card-media :src="require('./images/judokwaikemzeke.jpg')" height="500">
                    <v-container fill-height fluid>
                        <v-layout fill-height>
                            <v-flex xs6 align-end flexbox>
                                <div class="white--text" style="padding:5px;background-color:rgba(34,34,34,0.8)">
                                    <h3 class="headline">Judokwai Kemzeke</h3>
                                    <p>
                                        Deze florerende judoclub bestaat al ongeveer 40 jaar, telt ongeveer 100-150 leden en heeft al veel op haar palmares.
                                    </p>
                                    <p>
                                        Alle informatie betreffende trainingen, contactgegevens, geschiedenis van de club en het judo, foto’s van leden/kampen/tornooien.
                                    </p>
                                </div>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-media>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        data() {
            return {};
        }
    };
</script>
