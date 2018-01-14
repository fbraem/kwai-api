<template>
    <v-card :flat="complete">
        <div v-if="complete">
            <v-card-media v-if="story.header_detail_crop" :src="story.header_detail_crop" height="400px">
                <v-container fill-height fluid>
                    <v-layout fill-height>
                        <v-flex align-end flexbox>
                            <span class="pa-2 headline" style="background-color:rgba(255,255,255,0.7)">
                                {{ title }}
                                <v-icon v-if="isNew" color="red" light style="margin-left:5px;margin-top:-7px">
                                    fa-star
                                </v-icon>
                            </span>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-media>
            <v-card-title>
                <div>
                    <h3 v-if="!story.header_detail_crop" class="headline mb-0">
                        <v-icon v-if="isNew" color="red" light style="float:right;margin-left:5px">
                            fa-star
                        </v-icon>
                        {{ title }}
                    </h3>
                    <div class="news-mini-meta">
                        <span v-if="category"><router-link :to="{ name: 'news.category', params: { category_id : category.id} }">{{ category.name }}</router-link> &bull; </span>
                        <span v-if="authorName.length > 0">{{ authorName }} &bull; </span>
                        <span v-if="story.publish_date">{{ $t('published', { publishDate : publishDate, publishDateFromNow : publishDateFromNow }) }}</span>
                    </div>
                    <div v-html="summary" style="margin-top:20px" :class="{ 'news-meta' : complete }">
                    </div>
                </div>
            </v-card-title>
        </div>
        <div v-else>
            <v-card-media v-if="story.header_overview_crop" :src="story.header_overview_crop" height="200px" />
            <v-card-title>
                <div>
                    <h3 class="mb-0">
                        <v-icon v-if="isNew" color="red" light style="float:right;margin-left:5px">
                            fa-star
                        </v-icon>
                        {{ title }}
                    </h3>
                    <div class="news-mini-meta">
                        <span v-if="category"><router-link :to="{ name: 'news.category', params: { category_id : category.id} }">{{ category.name }}</router-link> &bull; </span>
                        <span v-if="authorName.length > 0">{{ authorName }} | </span>
                        <span v-if="story.publish_date">{{ $t('published', { publishDate : publishDate, publishDateFromNow : publishDateFromNow }) }}</span>
                    </div>
                    <div v-html="summary" style="margin-top:20px" :class="{ 'news-meta' : complete }">
                    </div>
                </div>
            </v-card-title>
        </div>
        <v-divider v-if="complete"></v-divider>
        <v-card-text v-if="complete">
            <div class="news-content" v-html="content">
            </div>
        </v-card-text>
        <v-card-actions>
            <v-btn v-if="!complete && content.length > 0" icon :to="{ name : 'news.story' , params : { id : story.id }}" flat>
                <v-icon>fa-ellipsis-h</v-icon>
            </v-btn>
            <v-btn v-if="complete" icon :to="{ name : 'news.browse' }" flat>
                <v-icon>view_list</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn v-if="!featured && $isAllowed('update', story)" color="secondary" icon :to="{ name : 'news.update', params : { id : story.id }}" flat>
                <v-icon>fa-edit</v-icon>
            </v-btn>
            <v-btn v-if="!featured && $isAllowed('remove', story)" color="secondary" icon @click="$emit('delete')" flat>
                <v-icon>fa-trash</v-icon>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<style>
    .news-mini-meta {
        font-size: 12px;
        color: #999;
    }
    .news-meta {
        color: #999;
    }
    .news-content img {
        max-width: 100%;
    }

    blockquote {
      background: #f9f9f9;
      border-left: 10px solid #ccc;
      margin: 1.5em 10px;
      padding: 0.5em 10px;
      quotes: "\201C""\201D""\2018""\2019";
    }
    blockquote p {
      display: inline;
    }
</style>

<script>
    import moment from 'moment';
    import find from 'lodash/find';
    import filter from 'lodash/filter';

    import messages from '../lang/NewsCard';

    export default {
        i18n : {
            messages : messages
        },
        props : {
            story : {
                type : Object,
                required : true
            },
            complete : {
                type : Boolean,
                required : true
            },
            featured : {
                type : Boolean,
                default : false
            }
        },
        computed : {
            summary() {
                var content = find(this.story.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.html_summary;
                }
                return "";
            },
            content() {
                var content = find(this.story.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.html_content;
                }
                return "";
            },
            title() {
                var content = find(this.story.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.title;
                }
                return "";
            },
            publishDate() {
                var utc = moment.utc(this.story.publish_date, 'YYYY-MM-DD HH:mm:ss');
                return utc.local().format('L');
            },
            publishDateFromNow() {
                var utc = moment.utc(this.story.publish_date, 'YYYY-MM-DD HH:mm:ss');
                return utc.local().fromNow();
            },
            isNew() {
                var utc = moment.utc(this.story.publish_date, 'YYYY-MM-DD HH:mm:ss');
                return moment().diff(utc.local(), 'weeks') < 1;
            },
            authorName() {
                var author = this.story.author;
                if (author) {
                    return filter([author.first_name, author.last_name]).join(' ');
                }
                return "";
            },
            category() {
                return this.$store.getters['categoryModule/category'](this.story.category.id);
            }
        }
    }
</script>
