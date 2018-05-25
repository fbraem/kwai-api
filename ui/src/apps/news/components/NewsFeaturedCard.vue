<template>
    <div>
        <div class="uk-card uk-card-default uk-card-body uk-card-small uk-flex uk-flex-middle" style="border:1px solid rgba(0,0,0,0.075);">
            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle" uk-grid>
                <div class="uk-width-1-3@s uk-width-2-5@m uk-height-1-1">
                    <img src="https://picsum.photos/500/500/?random=1" alt="">
                </div>
                <div class="uk-width-2-3@s uk-width-3-5@m">
                    <span class="uk-label uk-label-warning" style="font-size: 0.75rem">{{ story.category.name }}</span>
                    <h3 class="uk-card-title uk-margin-small-top uk-margin-remove-bottom">
                        <a class="uk-link-reset" href="#">{{ story.title }}</a>
                    </h3>
                    <span class="uk-article-meta" v-if="story.publish_date">{{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}</span>
                    <div class="uk-margin-small" v-html="story.summary"></div>
                </div>
            </div>
        </div>
    </div>
</template>

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
            }
        },
        computed : {
            contentLink() {
                if ( !this.complete && this.content.length > 0) {
                    return {
                        name : 'news.story',
                        params : {
                            id : this.story.id
                        }
                    };
                }
                return null;
            },
            category() {
                return this.$store.getters['categoryModule/category'](this.story.category.id);
            }
        }
    }
</script>
