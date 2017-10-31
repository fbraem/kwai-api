<template>
    <v-card :flat="complete">
        <v-card-media v-if="complete && story.header_detail_crop" :src="story.header_detail_crop" height="200px" />
        <v-card-media v-if="!complete && story.header_overview_crop" :src="story.header_overview_crop" height="200px" />
        <v-card-title>
            <div>
                <h3 class="headline mb-0">{{ story.title }}</h3>
                <div class="mini-meta">
                    Published {{ publishDateFromNow }}
                    <span v-if="authorName.length > 0"> - Written by {{ authorName }}</span>
                </div>
                <div v-html="summaryHtml" style="margin-top:20px" :class="{ 'meta' : complete }">
                </div>
            </div>
        </v-card-title>
        <v-divider v-if="complete"></v-divider>
        <v-card-text v-if="complete">
            <div v-html="contentHtml">
            </div>
        </v-card-text>
        <v-card-actions>
            <v-btn v-if="!complete && story.content != story.content.length > 0" icon :to="'/read/' + story.id" flat>
                <v-icon>more_horiz</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn v-if="$isAllowed('update', story)" icon :to="'/update/' + story.id" flat>
                <v-icon>mode_edit</v-icon>
            </v-btn>
            <v-btn v-if="$isAllowed('remove', story)" icon :to="'/delete/' + story.id" flat>
                <v-icon>delete</v-icon>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<style scoped>
    .mini-meta {
        font-size: 12px;
        color: #999;
    }
    .meta {
        color: #999;
    }
</style>

<script>
    import marked from 'marked';
    import moment from 'moment';
    import _ from 'lodash';

    export default {
        props : {
            'story' : {
                type : Object,
                required : true
            },
            'complete' : {
                type : Boolean,
                required : true
            }
        },
        computed : {
            summaryHtml() {
                if (this.story.summary) {
                    return marked(this.story.summary, { sanitize : true });
                }
                return '';
            },
            contentHtml() {
                if (this.story.content) {
                    return marked(this.story.content, { sanitize : true });
                }
                return '';
            },
            publishDateFromNow() {
                return moment(this.story.publish_date, 'YYYY-MM-DD HH:mm:ss').fromNow();
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
