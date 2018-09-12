<template>
    <div class="uk-container uk-margin-top">
        <section class="uk-section uk-section-small uk-section-secondary">
            <div class="uk-container uk-margin-left uk-margin-right">
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
            <div class="uk-container">
                <div class="uk-grid-small uk-flex-center uk-child-width-1-3@s uk-child-width-1-6@l" uk-grid>
                    <div>
                        <router-link :to="{ name : 'news.browse' }" class="uk-link-reset">
                            <div class="uk-card uk-card-hover uk-card-body uk-text-center" style="background-color:hsla(0,0%,94%,.9)">
                                <fa-icon name="newspaper" scale="2" style="color:#f44336;height:32px;" />
                                <div class="uk-text-uppercase uk-margin-top">Nieuws</div>
                            </div>
                        </router-link>
                    </div>
                    <div>
                        <router-link :to="{ name : 'categories.read', params : { id : '2' } }" class="uk-link-reset">
                            <div class="uk-card uk-card-hover uk-card-body uk-text-center" style="background-color:hsla(0,0%,94%,.9)">
                                <fa-icon name="university" scale="2" style="color:#f44336;height:32px;" />
                                <div class="uk-text-uppercase uk-margin-top">Trainingen</div>
                            </div>
                        </router-link>
                    </div>
                    <div>
                        <router-link :to="{ name : 'categories.read', params : { id : '3'} }" class="uk-link-reset">
                            <div class="uk-card uk-card-hover uk-card-body uk-text-center" style="background-color:hsla(0,0%,94%,.9)">
                                <fa-icon name="trophy" scale="2" style="color:#f44336;height:32px;" />
                                <div class="uk-text-uppercase uk-margin-top">Tornooien</div>
                            </div>
                        </router-link>
                    </div>
                    <div>
                        <a href="https://www.judokwaikemzeke.be/oud/kalender.htm" class="uk-link-reset">
                            <div class="uk-card uk-card-hover uk-card-body uk-text-center" style="background-color:hsla(0,0%,94%,.9)">
                                <fa-icon name="calendar" scale="2" style="color:#f44336;height:32px;" />
                                <div class="uk-text-uppercase uk-margin-top">Kalender</div>
                            </div>
                        </a>
                    </div>
    <!--
                    <div class="uk-width-small">
                        <a class="uk-link-muted uk-flex uk-flex-column uk-flex-center uk-button uk-button-default uk-width-1-1 btn">
                            <fa-icon name="shopping-basket" scale="2" style="color:#f44336" />
                            Materiaal
                        </a>
                    </div>
    -->
                    <div>
                        <a href="https://www.judokwaikemzeke.be/oud/training/trainers.htm" class="uk-link-reset">
                            <div class="uk-card uk-card-hover uk-card-body uk-text-center" style="background-color:hsla(0,0%,94%,.9)">
                                <fa-icon name="users" scale="2" style="color:#f44336;height:32px;"/>
                                <div class="uk-text-uppercase uk-margin-top">Bestuur</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <section class="uk-section uk-section-small uk-padding-remove-top">
            <div class="uk-container uk-container-expand">
                <div v-if="loading" class="uk-flex-center" uk-grid>
                    <div class="uk-text-center">
                        <fa-icon name="spinner" scale="2" spin />
                    </div>
                </div>
                <h4 class="uk-heading-line uk-text-bold" id="newsgrid"><span>Belangrijk Nieuws</span></h4>
                <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories"></Paginator>
                <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-grid" uk-grid>
                    <NewsCard v-for="story in stories" :story="story" :key="story.id"></NewsCard>
                </div>
                <div style="clear:both"></div>
                <Paginator v-if="storiesMeta" :count="storiesMeta.count" :limit="storiesMeta.limit" :offset="storiesMeta.offset" @page="loadStories"></Paginator>
                <router-link :to="{ name : 'news.browse' }">
                    {{ $t('more_news') }}
                </router-link>
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
                                    <p>Voor het vierde jaar op rij verdient onze club goud bij de proclomatie van het jeugdjudofonds!</p>
                                </div>
                                <div>
                                    <img :src="require('./images/goud_jeugdsport_2018.png')" style="height:125px" alt="">
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
                                    <p>De <a href="https://www.vjf.be">Vlaamse Judo Federatie</a> en Judokwai Kemzeke bieden u 4 gratis proeflessen aan.</p>
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
                    <div>
                        <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                            <h3 class="uk-card-title">Gezond sporten</h3>
                            <div class="uk-flex-center" uk-grid>
                                <div>
                                    <p>Onze club draagt <a href="https://www.vjf.be/nl/aanvulling-en-aanpassing-vjf-website-gezond-en-ethisch-sporten">Gezond Sporten</a> hoog in het het vaandel.</p>
                                </div>
                                <div style="background-color:white;padding:10px">
                                    <img :src="require('./images/gezond.jpg')" style="height:125px" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="uk-card uk-card-small uk-card-default uk-card-body uk-light message-card">
                            <h3 class="uk-card-title">Panathlon Verklaring</h3>
                            <div class="uk-flex-center" uk-grid>
                                <div class="uk-width-1-1">
                                    <p>Onze club onderschrijft de <a href="http://panathlonvlaanderen.be">Panathlon</a> verklaring.</p>
                                </div>
                                <div style="background-color:white;padding:10px">
                                    <img :src="require('./images/panathlon.jpg')" style="height:125px" alt="">
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
    import 'vue-awesome/icons/spinner';

    import NewsCard from '@/apps/news/components/NewsCard.vue';
    import Paginator from '@/components/Paginator.vue';

    import newsStore from '@/apps/news/store';

    import UIKit from 'uikit';

    import messages from './lang';

    export default {
        i18n : messages,
        components : {
            NewsCard,
            Paginator
        },
        data() {
            return {};
        },
        computed : {
            loading() {
                return this.$store.getters['newsModule/loading'];
            },
            stories() {
                return this.$store.getters['newsModule/stories'];
            },
            storiesMeta() {
                return this.$store.getters['newsModule/meta'];
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
        },
        methods : {
            async loadStories(offset) {
                try {
                    await this.$store.dispatch('newsModule/browse', {offset : offset, featured : true});
                    var el = document.getElementById('newsgrid');
                    el.scrollIntoView();
                } catch(error) {
                    console.log(error);
                }
            }
        }
    };
</script>
