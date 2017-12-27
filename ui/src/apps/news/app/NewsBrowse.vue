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
                <NewsCard :story="story" :complete="false" />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import moment from 'moment';
    import NewsCard from '../components/NewsCard.vue';

    export default {
        components : {
            NewsCard
        },
        props : {
            year : null,
            month : null,
            category_id : null
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
        methods : {
            fetchData() {
                this.$store.dispatch('newsModule/browse', {
                    category : this.category_id,
                    year : this.year,
                    month : this.month
                });
            }
        }
    };
</script>
