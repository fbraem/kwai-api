<template>
    <div>
        <div class="uk-card uk-card-body uk-card-default uk-card-small" style="border:1px solid rgba(0,0,0,0.075);">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle" uk-grid>
                <div v-if="story.header_overview_crop" class="uk-width-1-3@s uk-width-2-5@m uk-height-1-1">
                    <img :src="story.header_overview_crop" alt="" />
                </div>
                <div :class="widthClass">
                    <span class="uk-label uk-label-warning" style="font-size: 0.75rem">{{ story.category.name }}</span>
                    <h3 class="uk-card-title uk-margin-small-top uk-margin-remove-bottom">
                        <router-link v-if="story.content" class="uk-link-reset" :to="contentLink">{{ story.title }}</router-link>
                        <span v-else>{{ story.title }}</span>
                    </h3>
                    <span class="uk-article-meta" v-if="story.publish_date">{{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}</span>
                    <div class="uk-margin-small" v-html="story.summary"></div>
                    <router-link v-if="story.content" class="uk-icon-button uk-float-right" :to="contentLink">
                        <fa-icon name="ellipsis-h" />
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/ellipsis-h';
    import messages from '../lang';

    export default {
        i18n : messages,
        props : {
            story : {
                type : Object,
                required : true
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
                        'uk-width-1-3@s' : true,
                        'uk-width-3-5@m' : true
                    };
                }
                return {
                    'uk-width-1-1' : true
                };
            }
        }
    }
</script>
