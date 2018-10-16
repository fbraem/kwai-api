<template>
    <div>
        <PageHeader v-if="story" :picture="story.detail_picture">
            <div uk-grid>
                <div class="uk-width-expand">
                    <div class="uk-card uk-card-body">
                        <div class="uk-card-badge uk-label" style="font-size: 0.75rem;background-color:#c61c18;color:white">
                            <router-link :to="{ name : 'news.category', params : { category : story.category.id }}" class="uk-link-reset">
                                {{ story.category.name }}
                            </router-link>
                        </div>
                        <div class="uk-light">
                            <h1 class="uk-margin-remove">{{ $t('news')}}</h1>
                            <h2 class="uk-margin-remove">{{ story.title }}</h2>
                            <div class="uk-article-meta" v-if="story.publish_date">{{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}</div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-1-6@m">
                    <div class="uk-flex uk-flex-right">
                        <div v-if="$story.isAllowed('update', story)" class="uk-margin-small-left">
                            <router-link :to="{ name : 'news.update', params : { id : story.id }}" class="uk-icon-button">
                                <fa-icon name="edit" />
                            </router-link>
                        </div>
                        <div v-if="$story.isAllowed('remove', story)" class="uk-margin-small-left">
                            <a uk-toggle="target: #delete-story" class="uk-icon-button">
                                <fa-icon name="trash" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </PageHeader>
        <Page>
            <div v-if="$wait.is('news.read')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <article v-if="story" class="uk-section uk-section-small uk-padding-remove-top">
                <blockquote>
                    <div v-html="story.summary"></div>
                </blockquote>
                <div class="news-content" v-html="story.content">
                </div>
                <AreYouSure id="delete-story" :yes="$t('delete')" :no="$t('cancel')" @sure="deleteStory">
                    {{ $t('are_you_sure') }}
                </AreYouSure>
            </article>
        </Page>
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

    import messages from './lang';

    import Page from './Page.vue';
    import PageHeader from '@/site/components/PageHeader.vue';
    import AreYouSure from '@/components/AreYouSure.vue';

    import newsStore from '@/stores/news';

    export default {
        components : {
            Page,
            PageHeader,
            AreYouSure
        },
        i18n : messages,
        computed : {
            story() {
                return this.$store.getters['newsModule/story'](this.$route.params.id);
            },
            picture() {
                if (this.story) {
                    return this.story.header_detail_crop;
                }
                return null;
            },
            facebookUrl() {
                return "https://www.judokwaikemzeke.be/facebook/news/" + this.story.id;
            },
            error() {
                return this.$store.getters['newsModule/error'];
            }
        },
        beforeCreate() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', newsStore);
            }
        },
        async created() {
            await this.fetchData();
        },
        async beforeRouteUpdate(to, from, next) {
            await this.fetchData();
            next();
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
