<template>
    <v-container>
        <v-layout v-if="category">
            <v-flex xs12>
                <h1 class="display-1">{{ category.name }}</h1>
                <div class="category-description">
                    {{ category.description }}
                </div>
            </v-flex>
        </v-layout>
        <v-layout v-if="year && month">
            <v-flex xs12>
                <h1 class="display-1">{{ $t('archive_title', { monthName : monthName, year : year }) }}</h1>
            </v-flex>
        </v-layout>
        <v-layout v-if="noNews" row wrap>
            <v-flex xs12>
                {{ $t('no_news') }}
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex v-for="story in stories" :key="story.id" xs12 sm6 md5 lg4>
                <NewsCard :story="story" :complete="false" @delete="areYouSure(story.id)" />
            </v-flex>
        </v-layout>
        <v-dialog v-model="showAreYouSure" max-width="290">
            <v-card>
                <v-card-text>
                    <v-layout>
                        <v-flex xs2>
                            <v-icon color="error">fa-bell</v-icon>
                        </v-flex>
                        <v-flex xs10>
                            <div>{{ $t('sure_to_delete') }}</div>
                        </v-flex>
                    </v-layout>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" @click="deleteStory">
                        <v-icon left>fa-trash</v-icon>
                        {{ $t('delete') }}
                    </v-btn>
                    <v-btn @click="showAreYouSure = false">
                        <v-icon left>fa-ban</v-icon>
                        {{ $t('cancel') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import moment from 'moment';
    import NewsCard from '../components/NewsCard.vue';

    export default {
        components : {
            NewsCard
        },
        props : [
            'year',
            'month',
            'category_id'
        ],
        data() {
            return {
                showAreYouSure : false,
                storyToDelete : null
            };
        },
        computed : {
            stories() {
                return this.$store.state.newsModule.stories;
            },
            noNews() {
                return !this.stories || this.stories.length == 0;
            },
            category() {
                if (this.category_id) {
                    return this.$store.getters['newsModule/category'](this.category_id);
                }
                return null;
            },
            monthName() {
                return moment.months()[this.month -1];
            }
          },
        created() {
            this.fetchData();
        },
        watch : {
            '$route'() {
                this.year = this.$route.params.year;
                this.month = this.$route.params.month;
                this.category_id = this.$route.params.category_id;
                this.fetchData();
            }
        },
        methods : {
            fetchData() {
                this.$store.dispatch('newsModule/browse', {
                    category : this.category_id,
                    year : this.year,
                    month : this.month
                });
            },
            areYouSure(id) {
                this.showAreYouSure = true;
                this.storyToDelete = id;
            },
            deleteStory() {
                this.showAreYouSure = false;
                this.$store.dispatch('newsModule/delete', {
                    id : this.storyToDelete
                });
            }
        }
    };
</script>
