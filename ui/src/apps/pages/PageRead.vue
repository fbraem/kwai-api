<template>
  <div>
    <Page>
      <div v-if="error">
        {{ error.response.statusText }}
      </div>
      <Spinner v-if="$wait.is('pages.read')" />
      <section
        v-if="page"
        class="uk-section uk-section-small uk-padding-remove-top"
      >
        <div
          class="uk-flex-center"
          uk-grid
        >
          <figure v-if="page.header_detail_crop">
            <img :src="page.header_detail_crop" />
          </figure>
          <article
            class="page-content uk-article"
            style="overflow-x: auto;"
            v-html="page.content.html_content"
          >
          </article>
        </div>
      </section>
    </Page>
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
        display: block;
        overflow-x: auto;
        white-space: nowrap;
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
import messages from './lang';

import Page from './Page.vue';
import Spinner from '@/components/Spinner.vue';

/**
 * Page for an information page
 */
export default {
  components: {
    Page,
    Spinner
  },
  i18n: messages,
  computed: {
    page() {
      return this.$store.getters['page/page'](this.$route.params.id);
    },
    categoryLink() {
      return {
        name: 'pages.category',
        params: {
          category:
          this.page.category.id
        }
      };
    },
    error() {
      return this.$store.state.page.error;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
    next();
  },
  methods: {
    fetchData(params) {
      try {
        this.$store.dispatch('page/read', { id: params.id });
      } catch (error) {
        console.log(error);
      }
    },
    deletePage() {
      var category = this.page.category.id;
      this.$store.dispatch('page/delete', {
        page: this.page
      }).then(() => {
        this.$router.push({
          name: 'pages.browse',
          params: {
            category: category
          }
        });
      });
    }
  }
};
</script>
