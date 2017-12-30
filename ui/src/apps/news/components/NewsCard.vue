<template>
    <v-card :flat="complete">
        <div v-if="complete">
            <v-card-media v-if="story.header_detail_crop" :src="story.header_detail_crop" height="400px">
                <v-container fill-height fluid>
                    <v-layout fill-height>
                        <v-flex xs8 align-end flexbox>
                            <div class="pa-2 white headline" style="background-color:rgba(34,34,34,0.8)">
                                <v-icon v-if="isNew" color="red" light style="float:right">
                                    fa-star
                                </v-icon>
                                {{ title }}
                            </div>
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
                        <span v-if="authorName.length > 0">{{ authorName }} | </span>
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
                    <h3 class="headline mb-0">
                        <v-icon v-if="isNew" color="red" light style="float:right">
                            fa-star
                        </v-icon>
                        {{ title }}
                    </h3>
                    <div class="news-mini-meta">
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
            <v-btn v-if="!complete && content.length > 0" icon :href="'news.html#/story/' + story.id" flat>
                <v-icon>fa-ellipsis-h</v-icon>
            </v-btn>
            <v-btn v-if="complete" icon :to="'/'" flat>
                <v-icon>view_list</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn v-if="$isAllowed('update', story)" color="secondary" icon :to="'/update/' + story.id" flat>
                <v-icon>fa-edit</v-icon>
            </v-btn>
            <v-btn v-if="$isAllowed('remove', story)" color="secondary" icon @click="$emit('delete')" flat>
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
</style>

<script>
    import moment from 'moment';
    import _ from 'lodash';

    import messages from '../lang/NewsCardLang';

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
            }
        },
        computed : {
            summary() {
                var content = _.find(this.story.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.html_summary;
                }
                return "";
            },
            content() {
                var content = _.find(this.story.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.html_content;
                }
                return "";
            },
            title() {
                var content = _.find(this.story.contents, function(o) {
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
                    return _.filter([author.first_name, author.last_name]).join(' ');
                }
                return "";
            }
        }
    }
</script>
