<template>
    <v-container class="pt-0">
        <v-layout>
            <v-flex xs12>
                <v-card v-if="page" flat style="flex:1">
                    <v-card-media v-if="page.crop" :src="page.crop" :height="imageHeight" class="no-print">
                        <v-container fill-height fluid>
                            <v-layout fill-height>
                                <v-flex align-end flexbox>
                                    <span class="pa-2 headline" style="background-color:rgba(255,255,255,0.7)">
                                        {{ title }}
                                    </span>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-media>
                    <v-card-title>
                        <div>
                            <h3 v-if="!page.crop" class="headline mb-0">
                                {{ title }}
                            </h3>
                            <div class="page-mini-meta">
                                <span v-if="category">
                                    <router-link :to="{ name: 'pages.category', params: { category_id : category.id} }">{{ category.name }}</router-link>
                                </span>
                                <span v-if="authorName.length > 0">{{ authorName }} &bull; </span>
                            </div>
                            <div v-html="summary" style="margin-top:10px" class="page-meta">
                            </div>
                        </div>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <div class="page-content" v-html="content">
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn icon :to="{ name : 'pages' }" flat>
                            <v-icon>view_list</v-icon>
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn v-if="$isAllowed('update', page)" color="secondary" icon :to="{ name : 'pages.update', params : { id : page.id }}" flat>
                            <v-icon>fa-edit</v-icon>
                        </v-btn>
                        <v-btn v-if="$isAllowed('remove', page)" color="secondary" icon @click="areYouSure()" flat>
                            <v-icon>fa-trash</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
                <v-alert v-if="!page && !loading" color="error" value="true" icon="fa-exclamation-triangle">
                    {{ $t('not_found') }}
                </v-alert>
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
                    <v-btn color="error" @click="deletePage">
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

<style>
    .page-mini-meta {
        font-size: 12px;
        color: #999;
    }

    .page-content table {
        border-collapse: collapse;
        margin-bottom:20px;
        margin-left:auto;
        margin-right:auto;
    }

    .page-content table tbody tr:nth-child(odd) {
        background: #eee;
    }
    .page-content table th,
    .page-content table td {
        border: 1px solid black;
        padding: .5em 1em;
    }

    .page-content blockquote {
      background: #f9f9f9;
      border-left: 10px solid #ccc;
      margin: 1.5em 10px;
      padding: 0.5em 10px;
      quotes: "\201C""\201D""\2018""\2019";
    }
    .page-content blockquote p {
      display: inline;
    }
    .page-content h3 {
        font-size: 24px;
        font-weight: 400;
        line-height: 32px;
        letter-spacing: normal;
    }
    .page-content ul {
        list-style-position: inside;
        margin-bottom: 20px;
    }

    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>

<script>
    import find from 'lodash/find';
    import filter from 'lodash/filter';

    import messages from '../lang/PageRead';

    export default {
        i18n : {
            messages
        },
        components : {
        },
        data() {
            return {
                showAreYouSure : false
            }
        },
        computed : {
            imageHeight() {
                switch(this.$vuetify.breakpoint.name) {
                    case 'xs':
                        return '200px';
                    case 'sm':
                        return '250px';
                    case 'md':
                        return '300px';
                    case 'lg':
                        return '400px';
                    case 'xl':
                        return '400px';
                }
            },
            page() {
                return this.$store.getters['pageModule/page'](this.$route.params.id);
            },
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
            },
            loading() {
                return this.$store.getters['pageModule/loading'];
            },
            category() {
                return this.$store.getters['categoryModule/category'](this.page.category.id);
            }
        },
        mounted() {
          this.$store.dispatch('pageModule/read', { id : this.$route.params.id })
            .catch((error) => {
              console.log(error);
          });
        },
        methods : {
            areYouSure() {
                this.showAreYouSure = true;
            },
            deletePage() {
                this.showAreYouSure = false;
                this.$store.dispatch('pageModule/delete', {
                    id : this.page.id
                }).then(() => {
                    this.$router.go(-1);
                });
            }
        }
    };
</script>
