<template>
    <div class="uk-container">
        <article v-if="story" class="uk-section uk-section-small uk-padding-remove-top">
            <header>
                <span class="uk-label uk-label-warning uk-float-right" style="font-size: 0.75rem">
                    <router-link :to="{ name : 'news.category', params : { category_id : story.category.id }}" class="uk-link-reset">
                        {{ story.category.name }}
                    </router-link>
                </span>
                <h2 class="uk-margin-remove-adjacent uk-text-bold uk-margin-small-bottom">
                    {{ story.title }}
                </h2>
                <div>
                    <router-link v-if="$story.isAllowed('update', story)" :to="{ name : 'news.update', params : { id : story.id }}" class="uk-icon-button">
                        <fa-icon name="edit" />
                    </router-link>
                    <a v-if="$story.isAllowed('remove', story)" uk-toggle="target: #delete-story" class="uk-icon-button">
                        <fa-icon name="trash" />
                    </a>
                </div>
                <div class="uk-article-meta" v-if="story.publish_date">{{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}</div>
                <blockquote v-if="story.summary" v-html="story.summary">
                </blockquote>
            </header>
            <figure v-if="story.header_detail_crop">
                <img :src="story.header_detail_crop"  />
            </figure>
            <div class="news-content" v-html="story.content">
            </div>
        </article>
        <NewsDelete @deleteStoryEvent="deleteStory" />
    </div>
</template>

<style>
    .news-content ul {
        list-style-position: inside;
        margin-bottom: 20px;
    }

    blockquote {
      background: #f9f9f9;
      border-left: 10px solid #ccc;
      margin: 1.5em 10px;
      padding: 0.5em 10px;
      quotes: "\201C""\201D""\2018""\2019";
    }
    .gallery {
        background: #eee;
        column-count: 4;
        column-gap: 1em;
        padding-left: 1em;
        padding-top: 1em;
        padding-right: 1em;
    }
    .gallery .item {
        background: white;
        display: inline-block;
        margin: 0 0 1em;
        width: 100%;
        padding: 1em;
    }
    @media (max-width: 1200px) {
      .gallery {
      column-count: 4;
      }
    }
    @media (max-width: 1000px) {
      .gallery {
          column-count: 3;
      }
    }
    @media (max-width: 800px) {
      .gallery {
          column-count: 2;
      }
    }
    @media (max-width: 400px) {
      .gallery {
          column-count: 1;
      }
    }
</style>

<script>
    import 'vue-awesome/icons/edit';
    import 'vue-awesome/icons/trash';

    import messages from '../lang';

    import NewsDelete from './NewsDelete.vue';

    export default {
        components : {
            NewsDelete
        },
        i18n : messages,
        computed : {
            story() {
                return this.$store.getters['newsModule/story'](this.$route.params.id);
            },
            loading() {
                return this.$store.getters['newsModule/loading'];
            }
        },
        mounted() {
            this.fetchData();
        },
        watch : {
            '$route'() {
                this.fetchData();
            }
        },
        methods : {
            fetchData() {
                try {
                    this.$store.dispatch('newsModule/read', { id : this.$route.params.id });
                }
                catch(error) {
                  console.log('error');
                  console.log(error);
                }
            },
            deleteStory() {
                this.$store.dispatch('newsModule/delete', {
                    story : this.story
                }).then(() => {
                    //this.$router.go(-1);
                });
            }
        }
    };
</script>
