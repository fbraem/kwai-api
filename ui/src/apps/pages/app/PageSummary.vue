<template>
    <v-card :to="{ name : 'pages.read' , params : { id : page.id }}">
        <span style="float:right;">
            <v-icon style="margin:10px;">fa-ellipsis-h</v-icon>
        </span>
        <v-card-title>
            <div class="headline">
                {{ title }}
            </div>
        </v-card-title>
        <v-card-text>
            <div v-html="summary">
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
    import find from 'lodash/find';
    import filter from 'lodash/filter';

    export default {
        props : [
            'page'
        ],
        computed : {
            summary() {
                var content = find(this.page.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.html_summary;
                }
                return "";
            },
            content() {
                var content = find(this.page.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.html_content;
                }
                return "";
            },
            title() {
                var content = find(this.page.contents, function(o) {
                    return o.locale == 'nl';
                });
                if (content) {
                    return content.title;
                }
                return "";
            },
            authorName() {
                var author = this.page.author;
                if (author) {
                    return filter([author.first_name, author.last_name]).join(' ');
                }
                return "";
            }
        }
    };
</script>
