<template>
    <div class="uk-container">
        <article v-if="story" class="uk-section uk-section-small uk-padding-remove-top">
            <header>
                <span class="uk-label uk-label-warning uk-float-right" style="font-size: 0.75rem">{{ story.category.name }}</span>
                <h2 class="uk-margin-remove-adjacent uk-text-bold uk-margin-small-bottom">{{ story.title }}</h2>
                <div class="uk-article-meta" v-if="story.publish_date">{{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}</div>
                <blockquote v-html="story.summary">
                </blockquote>
            </header>
            <figure v-if="story.header_detail_crop">
                <img :src="story.header_detail_crop"  />
            </figure>
            <div class="news-content" v-html="story.content">
            </div>
        </article>
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
    import messages from '../lang';

    export default {
        i18n : messages,
        data() {
            return {
                showAreYouSure : false,
                storyToDelete : null
            }
        },
        computed : {
            story() {
                return this.$store.getters['newsModule/story'](this.$route.params.id);
            },
            loading() {
                return this.$store.getters['newsModule/loading'];
            }
        },
        mounted() {
          this.$store.dispatch('newsModule/read', { id : this.$route.params.id })
            .catch((error) => {
              console.log(error);
          });
        },
        methods : {
            areYouSure(id) {
                this.showAreYouSure = true;
                this.storyToDelete = id;
            },
            deleteStory() {
                this.showAreYouSure = false;
                this.$store.dispatch('newsModule/delete', {
                    id : this.storyToDelete
                }).then(() => {
                    this.$router.go(-1);
                });
            }
        }
    };
</script>
