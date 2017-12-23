<template>
    <v-container grid-list-xl ma-0 pa-0>
        <v-layout>
            <v-flex xs12 lg10 offset-lg1>
                <v-card flat>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12 sm4 md3>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-card flat>
                                            <v-card-media :src="backgroundImage" height="200">
                                                <v-container class="button-container" fluid>
                                                    <v-flex xs8 sm8 md4 px-0>
                                                        <v-btn href="news.html" style="width:100%">
                                                            <v-icon large color="red">fa-newspaper</v-icon>
                                                            {{ $t('news') }}
                                                        </v-btn>
                                                    </v-flex>
                                                </v-container>
                                            </v-card-media>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-card>
                                            <v-card-title>
                                                <div class="headline">{{ $t('categories') }}</div>
                                            </v-card-title>
                                            <v-list two-line>
                                                <template v-for="(category, index) in categories">
                                                    <v-list-tile @click="selectCategory(category.id)">
                                                        <v-list-tile-content>
                                                            <v-list-tile-title>
                                                                {{ category.name }}
                                                            </v-list-tile-title>
                                                            <v-list-tile-sub-title>
                                                                <span class="category-description">{{ category.description }}</span>
                                                            </v-list-tile-sub-title>
                                                        </v-list-tile-content>
                                                        <v-list-tile-action>
                                                          <v-btn icon ripple>
                                                            <v-icon>fa-chevron-right</v-icon>
                                                          </v-btn>
                                                        </v-list-tile-action>
                                                    </v-list-tile>
                                                    <v-divider v-if="index != categories.length - 1"></v-divider>
                                                </template>
                                            </v-list>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-card>
                                            <v-card-title>
                                                <div class="headline">{{ $t('archive') }}</div>
                                            </v-card-title>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 sm8 md9>
                                <v-layout v-if="category">
                                    <v-flex xs12>
                                        <h1 class="display-1">{{ category.name }}</h1>
                                        <div class="category-description">
                                            {{ category.description }}
                                        </div>
                                    </v-flex>
                                </v-layout>
                                <v-layout v-if="noNews" row wrap>
                                    <v-flex xs12>
                                        {{ $t('no_news') }}
                                    </v-flex>
                                </v-layout>
                                <v-layout row wrap>
                                    <v-flex v-for="story in stories" :key="story.id" xs12 sm6 md5 lg4>
                                        <NewsCard :story="story" :complete="false" />
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex xs12>
                                <v-btn v-if="$isAllowed('create')" color="primary" icon :to="'create'" fab>
                                    <v-icon>fa-plus</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
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
    import URI from 'urijs';
    import NewsCard from './components/NewsCard.vue';

    export default {
        components : {
            NewsCard
        },
        data() {
            return {
                year : null,
                month : null
            };
        },
        computed : {
            stories() {
                return this.$store.state.newsModule.stories;
            },
            noNews() {
                return !this.stories || this.stories.length == 0;
            },
            categories() {
                return this.$store.getters['newsModule/categories']
            },
            backgroundImage() {
                return require('./images/news.jpg');
            },
            category() {
                if (this.$route.params.category) {
                    return this.$store.getters['newsModule/category'](this.$route.params.category);
                }
                return null;
            }
          },
        created() {
            this.$store.dispatch('newsModule/getCategories');
            this.fetchData();
        },
        watch : {
            '$route'() {
                this.fetchData();
            }
        },
        methods : {
            fetchData() {
                this.$store.dispatch('newsModule/browse', this.$route.params);
                this.year = this.$route.params.year;
                this.month = this.$route.params.month;
                this.year = this.$route.params.year;
            },
            selectCategory(id) {
                this.$router.push('/category/' + id);
            }
        }
    };
</script>
