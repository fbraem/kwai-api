<template>
    <div class="uk-container">
        <div v-if="loading" class="uk-flex-center" uk-grid>
            <div class="uk-text-center">
                <fa-icon name="spinner" scale="2" spin />
            </div>
        </div>
        <div v-else uk-grid class="uk-flex">
            <div v-if="category" class="uk-width-1-1">
                <div class="uk-tile uk-tile-muted uk-padding-small" style="border:1px solid rgba(0,0,0,0.075)">
                    <h3>{{ category.name }}</h3>
                    <div class="uk-text-meta">
                        {{ category.description }}
                    </div>
                </div>
            </div>
            <div v-if="year && month" class="uk-width-1-1">
                <div class="uk-tile uk-tile-muted uk-padding-small" style="border:1px solid rgba(0,0,0,0.075)">
                    <h3>{{ $t('archive_title', { monthName : monthName, year : year }) }}</h3>
                </div>
            </div>
            <div class="uk-child-width-1-1 uk-child-width-1-2@l" uk-grid="masonry: true">
                <NewsFeaturedCard v-for="story in stories" :story="story" :key="story.id"></NewsFeaturedCard>
            </div>
        </div>
    </div>
<!--
    <v-container style="padding-top:0px" v-else>
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
        <v-layout v-if="noNews && !featured" row wrap>
            <v-flex xs12>
                {{ $t('no_news') }}
            </v-flex>
        </v-layout>
        <v-layout v-else :justify-space-around="featured" row wrap>
            <v-flex v-for="story in stories" :key="story.id" xs12 sm6 md6 lg4 d-flex>
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
-->
</template>

<script>
    import 'vue-awesome/icons/spinner';

    import moment from 'moment';
    import NewsFeaturedCard from '../components/NewsFeaturedCard.vue';
    import Paginator from '@/components/Paginator.vue';

    import messages from '../lang';

    export default {
        i18n : messages,
        components : {
            NewsFeaturedCard,
            Paginator
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
            loading() {
                return this.$store.getters['newsModule/loading'];
            },
            stories() {
                return this.$store.getters['newsModule/stories'];
            },
            noNews() {
                return !this.stories || this.stories.length == 0;
            },
            category() {
                if (this.category_id) {
                    return this.$store.getters['categoryModule/category'](this.category_id);
                }
                return null;
            },
            monthName() {
                return moment.months()[this.month -1];
            }
          },
        mounted() {
            var categories = this.$store.getters['categoryModule/categories'];
            if (categories.length == 0) {
                this.$store.dispatch('categoryModule/browse');
            }
            this.fetchData({
                year : this.year,
                month : this.month,
                category : this.category_id,
                featured : this.featured
            });
        },
        watch : {
            '$route'() {
                this.fetchData({
                    year : this.$route.params.year,
                    month : this.$route.params.month,
                    category : this.$route.params.category_id
                });
            }
        },
        methods : {
            fetchData(payload) {
                this.$store.dispatch('newsModule/browse', payload);
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
            },
            readPage(offset) {
                console.log(offset);
            }
        }
    };
</script>
