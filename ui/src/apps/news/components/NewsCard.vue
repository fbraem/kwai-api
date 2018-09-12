<template>
    <div>
        <div class="uk-card uk-card-body uk-card-default uk-card-small uk-border-rounded" style="box-shadow:none;border:  1px solid rgba(0,0,0,0.075);">
            <div class="uk-child-width-1-1" uk-grid>
                <div>
                    <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle" uk-grid>
                        <div v-if="story.header_overview_crop" class="uk-width-1-3@s uk-width-2-5@m uk-height-1-1">
                            <img :src="story.header_overview_crop" alt="" />
                        </div>
                        <div :class="widthClass">
                            <span v-if="showCategory" class="uk-label uk-label-warning" style="font-size: 0.75rem">
                                <router-link :to="{ name : 'news.category', params : { category_id : story.category.id }}" class="uk-link-reset">
                                    {{ story.category.name }}
                                </router-link>
                            </span>
                            <h3 class="uk-card-title uk-margin-small-top uk-margin-remove-bottom uk-text-break">
                                <router-link v-if="story.content" class="uk-link-reset" :to="contentLink">{{ story.title }}</router-link>
                                <span v-else>{{ story.title }}</span>
                            </h3>
                            <span class="uk-article-meta" v-if="story.publish_date">
                                {{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}
                            </span>
                            <div class="uk-margin-small" v-html="story.summary"></div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="uk-float-right">
                        <router-link v-if="story.content" class="uk-icon-button" :to="contentLink">
                            <fa-icon name="ellipsis-h" />
                        </router-link>
                        <router-link v-if="$story.isAllowed('update', story)" :to="{ name : 'news.update', params : { id : story.id }}" class="uk-icon-button">
                            <fa-icon name="edit" />
                        </router-link>
                        <a v-if="$story.isAllowed('remove', story)" uk-toggle="target: #delete-story" class="uk-icon-button">
                            <fa-icon name="trash" />
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/ellipsis-h';
    import 'vue-awesome/icons/edit';
    import 'vue-awesome/icons/trash';

    import NewsDelete from '../app//NewsDelete.vue';

    import messages from '../lang';

    export default {
        components : {
            NewsDelete
        },
        i18n : messages,
        props : {
            story : {
                type : Object,
                required : true
            },
            showCategory : {
                type : Boolean,
                default : true
            }
        },
        computed : {
            contentLink() {
                return {
                    name : 'news.story',
                    params : {
                        id : this.story.id
                    }
                };
            },
            category() {
                return this.$store.getters['categoryModule/category'](this.story.category.id);
            },
            widthClass() {
                if ( this.story.header_overview_crop ) {
                    return {
                        'uk-width-2-3@s' : true,
                        'uk-width-3-5@m' : true
                    };
                }
                return {
                    'uk-width-1-1' : true
                };
            }
        },
        methods : {
            deleteStory() {
                this.$store.dispatch('newsModule/delete', {
                    story : this.story
                }).then(() => {
                    //this.$router.go(-1);
                });
            }
        }
    }
</script>
