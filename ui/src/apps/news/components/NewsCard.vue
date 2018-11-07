<template>
    <div>
        <div class="uk-card uk-card-body uk-card-default uk-card-small uk-border-rounded" style="box-shadow:none;border:  1px solid rgba(0,0,0,0.075);">
            <div v-if="showCategory" class="uk-card-badge uk-label" style="font-size: 0.75rem;background-color:#c61c18;">
                <router-link :to="{ name : 'news.category', params : { category : story.category.id }}" class="uk-link-reset">
                    {{ story.category.name }}
                </router-link>
            </div>
            <div class="uk-child-width-1-1" :class="{ 'uk-margin-medium-top' : showCategory }" uk-grid>
                <div>
                    <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle" uk-grid>
                        <div v-if="story.overview_picture" class="uk-width-1-3@s uk-width-2-5@m uk-width-4-6@l uk-height-1-1">
                            <img :src="story.overview_picture" alt="" />
                        </div>
                        <div :class="widthClass">
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
                <div class="uk-margin-remove-top">
                    <span class="uk-float-right">
                        <router-link v-if="story.content" class="uk-icon-button uk-link-reset" :to="contentLink">
                            <i class="fas fa-ellipsis-h"></i>
                        </router-link>
                        <router-link v-if="$story.isAllowed('update', story)" :to="{ name : 'news.update', params : { id : story.id }}" class="uk-icon-button uk-link-reset">
                            <i class="fas fa-edit"></i>
                        </router-link>
                        <a v-if="$story.isAllowed('remove', story)" @click="deleteStory" class="uk-icon-button uk-link-reset">
                            <i class="fas fa-trash"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import messages from '../lang';

    export default {
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
                if ( this.story.overview_picture ) {
                    return {
                        'uk-width-2-3@s' : true,
                        'uk-width-3-5@m' : true,
                        'uk-width-4-6@l' : true
                    };
                }
                return {
                    'uk-width-1-1' : true
                };
            }
        },
        methods : {
            deleteStory() {
                this.$emit('deleteStory', this.story);
            }
        }
    }
</script>
