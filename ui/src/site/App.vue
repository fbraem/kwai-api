<template>
    <div>
        <section class="uk-section uk-section-small uk-section-secondary">
            <div class="uk-container uk-container-expand">
                <div uk-grid>
                    <div class="uk-width-1-1 uk-width-2-3@m">
                        <img :src="require('./images/judokwaikemzeke.jpg')" alt="" />
                    </div>
                    <div class="uk-width-1-1 uk-width-1-3@m">
                        <div uk-grid>
                            <div class="uk-width-1-1">
                                <img class="uk-align-center" :src="require('./images/logo2.png')" style="width:121px;height:121px;" />
                            </div>
                            <div class="uk-width-1-1">
                                <h4 class="uk-margin-remove-top uk-text-center">Judokwai Kemzeke</h4>
                                {{ $t("message.hello") }}
                                <br />
                                {{ $t("message.info") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="uk-section uk-section-small">
            <div class="uk-grid-collapse uk-flex-center" uk-grid>
                <div class="uk-width-small">
                    <a :to="{ name : 'news.browse' }" class="uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                        <fa-icon name="newspaper" scale="2" style="color:#f44336" />
                        Nieuws
                    </a>
                </div>
                <div class="uk-width-small">
                    <a :to="{ name : 'news.browse' }" class="uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                        <fa-icon name="university" scale="2" style="color:#f44336" />
                        Trainingen
                    </a>
                </div>
                <div class="uk-width-small">
                    <a :to="{ name : 'news.browse' }" class="uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                        <fa-icon name="trophy" scale="2" style="color:#f44336" />
                        Tornooien
                    </a>
                </div>
                <div class="uk-width-small">
                    <a :to="{ name : 'news.browse' }" class="uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                        <fa-icon name="calendar" scale="2" style="color:#f44336" />
                        Kalender
                    </a>
                </div>
                <div class="uk-width-small">
                    <a :to="{ name : 'news.browse' }" class="uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                        <fa-icon name="shopping-basket" scale="2" style="color:#f44336" />
                        Materiaal
                    </a>
                </div>
                <div class="uk-width-small">
                    <a :to="{ name : 'news.browse' }" class="uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                        <fa-icon name="users" scale="2" style="color:#f44336"/>
                        Bestuur
                    </a>
                </div>
            </div>
        </section>
        <section class="uk-section uk-section-small uk-padding-remove-top">
            <div class="uk-container uk-container-expand">
                <h4 class="uk-heading-line uk-text-bold"><span>Belangrijk Nieuws</span></h4>
                <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@xl" uk-grid="masonry: true">
                    <NewsCard v-for="story in stories" :story="story" :key="story.id"></NewsCard>
                </div>
            </div>
        </section>
        <section class="uk-section uk-section-small">
            <div class="uk-container">
                <div class="uk-child-width-1-2@m uk-grid-small uk-grid-match" uk-grid>
                    <div>
                        <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                            <h3 class="uk-card-title">Jeugdvriendelijke Judoclub</h3>
                            <div class="uk-flex-center" uk-grid>
                                <div>
                                    <p>Voor het derde jaar op rij verdient onze club goud bij de proclomatie van het jeugdjudofonds!</p>
                                </div>
                                <div>
                                    <img :src="require('./images/goud_jeugdsport_2017.jpg')" style="height:125px" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                            <h3 class="uk-card-title" style="color:white">Locatie</h3>
                            <div class="uk-flex-center" uk-grid>
                                <div>
                                    <p>Wij trainen in de gevechtssportzaal van sportcentrum
                                        <strong>"De Sportstek"</strong> in Stekene, Nieuwstraat 60D.</p>
                                </div>
                                <div>
                                    <img :src="require('./images/sporthal.jpg')" style="height:125px" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                            <h3 class="uk-card-title">Eens proberen?</h3>
                            <div class="uk-flex-center" uk-grid>
                                <div>
                                    <p>De Vlaamse Judo Federatie en Judokwai Kemzeke bieden u 4 gratis proeflessen aan.</p>
                                </div>
                                <div>
                                    <img :src="require('./images/kim_ono.png')" style="height:125px;" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                            <h3 class="uk-card-title">Hartveilig</h3>
                            <div class="uk-flex-center" uk-grid>
                                <div>
                                    <p>Onze club is hartveilig. 10% van onze medewerkers zijn getraind in reanimatie.</p>
                                </div>
                                <div>
                                    <img :src="require('./images/hartveilig.jpg')" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style>
.btn {
    background-color:hsla(0,0%,94%,.9);
    height:9rem;
    margin:0;
    border-radius:0
}
.message-card {
    background-color:#607d8b;
}
.message-card h3 {
    color: white!important;
}
</style>

<script>
    import 'vue-awesome/icons/newspaper';
    import 'vue-awesome/icons/university';
    import 'vue-awesome/icons/trophy';
    import 'vue-awesome/icons/users';
    import 'vue-awesome/icons/calendar';
    import 'vue-awesome/icons/shopping-basket';

    import NewsCard from '@/apps/news/components/NewsCard.vue';
    import newsStore from '@/apps/news/store';

    export default {
        components : {
            NewsCard
        },
        data() {
            return {};
        },
        computed : {
            stories() {
                return this.$store.getters['newsModule/stories'];
            }
        },
        created() {
            this.$store.dispatch('setSubTitle', '');
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', newsStore);
            }
            this.$store.dispatch('newsModule/browse', {
                featured : true
            });
        }
    };
</script>
