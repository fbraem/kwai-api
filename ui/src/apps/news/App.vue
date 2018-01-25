<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex xs12 class="hidden-md-and-up">
                <v-menu offset-y>
                    <v-btn color="red" flat @click="drawer = !drawer" slot="activator">
                        <v-icon left>fa-bars</v-icon> {{ $t('news') }}
                    </v-btn>
                    <NewsSideBar :categories="categories" :archive="archive"></NewsSideBar>
                </v-menu>
            </v-flex>
            <v-flex md3 class="hidden-sm-and-down pl-3">
                <NewsSideBar :categories="categories" :archive="archive"></NewsSideBar>
            </v-flex>
            <v-flex xs12 md9>
                <v-container grid-list-xl class="pa-0">
                    <v-layout row wrap>
                        <v-flex xs12 class="pt-0">
                            <router-view name="NewsContent"></router-view>
                        </v-flex>
                        <v-flex xs12>
                            <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'news.create' }" fab small>
                                <v-icon>fa-plus</v-icon>
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<style>
.category-description {
    font-size: 12px;
    color: #999;
}

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
    import messages from './lang/App'

    import NewsSideBar from './app/SideBar.vue';

    export default {
        components : {
            NewsSideBar
        },
        i18n : {
            messages
        },
        data() {
            return {
                drawer : true
            };
        },
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            },
            archive() {
                return this.$store.getters['newsModule/archive'];
            }
          },
        created() {
            this.$store.dispatch('setSubTitle', this.$t('news'));
            this.$store.dispatch('categoryModule/browse');
            this.$store.dispatch('newsModule/loadArchive');
        }
    };
</script>
