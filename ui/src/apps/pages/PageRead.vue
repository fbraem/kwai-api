<template>
    <Page>
        <div v-if="error">
            {{ error.response.statusText }}
        </div>
        <div v-if="page" slot="title" class="uk-card uk-card-body uk-width-expand">
            <div class="uk-card-badge uk-label" style="font-size: 0.75rem;background-color:#c61c18;">
                <router-link :to="{ name : 'pages.category', params : { category : page.category.id }}" class="uk-link-reset">
                    {{ page.category.name }}
                </router-link>
            </div>
            <div class="uk-light">
                <h1>{{ page.title }}</h1>
            </div>
        </div>
        <template slot="toolbar">
            <div v-if="page && $page.isAllowed('update', page)" class="uk-margin-small-left">
                <router-link :to="{ name : 'pages.update', params : { id : page.id }}" class="uk-icon-button">
                    <fa-icon name="edit" />
                </router-link>
            </div>
            <div v-if="page && $page.isAllowed('remove', page)" class="uk-margin-small-left">
                <a uk-toggle="target: #delete-page" class="uk-icon-button">
                    <fa-icon name="trash" />
                </a>
            </div>
        </template>
        <article slot="content" v-if="page" class="uk-section uk-section-small">
            <blockquote class="uk-margin-small-left uk-margin-small-right" v-html="page.summary">
            </blockquote>
            <figure v-if="page.header_detail_crop">
                <img :src="page.header_detail_crop" />
            </figure>
            <article class="page-content uk-article" v-html="page.content">
            </article>
            <AreYouSure id="delete-page" :yes="$t('delete')" :no="$t('cancel')" @sure="deletePage">
                {{ $t('are_you_sure') }}
            </AreYouSure>
        </article>
    </Page>
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

    import messages from './lang';

    import Page from './Page.vue';
    import AreYouSure from '@/components/AreYouSure.vue';

    import pageStore from '@/stores/pages';

    export default {
        components : {
            Page,
            AreYouSure
        },
        i18n : messages,
        computed : {
            page() {
                return this.$store.getters['pageModule/page'](this.$route.params.id);
            },
            error() {
                return this.$store.getters['pageModule/error'];
            }
        },
        beforeCreate() {
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', pageStore);
            }
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                await vm.fetchData();
                next();
            });
        },
        methods : {
            fetchData() {
                try {
                    this.$store.dispatch('pageModule/read', { id : this.$route.params.id });
                }
                catch(error) {
                }
            },
            deletePage() {
                this.$store.dispatch('pageModule/delete', {
                    page : this.page
                }).then(() => {
                    this.$router.push({
                        name : 'pages.browse',
                        params : {
                            category : this.page.category
                        }
                    });
                });
            }
        }
    };
</script>
