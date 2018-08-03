<template>
    <div class="uk-container">
        <div v-if="error">
            {{ error.response.statusText }}
        </div>
        <div v-if="page" class="uk-label uk-label-warning uk-float-right uk-margin-small-top uk-margin-small-right" style="font-size: 0.75rem">
            <router-link :to="{ name : 'pages.category', params : { category_id : page.category.id }}" class="uk-link-reset">
                {{ page.category.name }}
            </router-link>
        </div>
        <section v-if="page" class="uk-section uk-section-small uk-section-secondary">
            <div class="uk-container">
                <div class="uk-flex uk-flex-center uk-flex-middle uk-light uk-text-center">
                    <div>
                        <h2>{{ page.title }}</h2>
                        <p class="uk-margin-small-left uk-margin-small-right" v-html="page.summary">
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <article v-if="page" class="uk-section uk-section-small">
            <div class="uk-flex uk-flex-center">
                <router-link v-if="$page.isAllowed('update', page)" :to="{ name : 'pages.update', params : { id : page.id }}" class="uk-icon-button">
                    <fa-icon name="edit" />
                </router-link>
                <a v-if="$page.isAllowed('remove', page)" uk-toggle="target: #delete-page" class="uk-icon-button">
                    <fa-icon name="trash" />
                </a>
            </div>
            <figure v-if="page.header_detail_crop">
                <img :src="page.header_detail_crop" />
            </figure>
            <article class="page-content uk-article" v-html="page.content">
            </article>
        </article>
        <PageDelete @deletePageEvent="deletePage" />
    </div>
</template>

<style>
    blockquote {
      background: #f9f9f9;
      border-left: 10px solid #ccc;
      margin: 1.5em 10px;
      padding: 0.5em 10px;
      quotes: "\201C""\201D""\2018""\2019";
    }

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

    .page-content .gallery {
        background: #eee;
        column-count: 4;
        column-gap: 1em;
        padding-left: 1em;
        padding-top: 1em;
        padding-right: 1em;
    }

    .page-content .gallery .item {
        background: white;
        display: inline-block;
        margin: 0 0 1em;
        /*width: 100%;*/
        padding: 1em;
    }

    .page-content .avatar {
        border-radius:50%;
        width:150px;
        height:150px;
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
    import 'vue-awesome/icons/edit';
    import 'vue-awesome/icons/trash';

    import messages from '../lang';

    import PageDelete from './PageDelete.vue';

    export default {
        components : {
            PageDelete
        },
        i18n : messages,
        computed : {
            page() {
                return this.$store.getters['pageModule/page'](this.$route.params.id);
            },
            loading() {
                return this.$store.getters['pageModule/loading'];
            },
            error() {
                return this.$store.getters['pageModule/error'];
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
                    this.$store.dispatch('pageModule/read', { id : this.$route.params.id });
                }
                catch(error) {
                  console.log('error');
                  console.log(error);
                }
            },
            deletePage() {
                this.$store.dispatch('pageModule/delete', {
                    page : this.page
                }).then(() => {
                    //this.$router.go(-1);
                });
            }
        }
    };
</script>
