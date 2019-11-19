<template>
  <!-- eslint-disable max-len -->
  <Header
    v-if="story"
    :title="$t('news')"
    :subtitle="story.content.title"
    :picture="picture"
    :badge="badge"
    :toolbar="toolbar"
  >
    <div
      v-if="story.publish_date"
      class="text-sm text-gray-600"
    >
      {{ $t('published', { publishDate : story.localPublishDate, publishDateFromNow : story.publishDateFromNow }) }}
    </div>
    <AreYouSure
      :show="showAreYouSure"
      @close="showAreYouSure = false;"
      :yes="$t('delete')"
      :no="$t('cancel')"
      @sure="deleteStory"
    >
    {{ $t('are_you_sure') }}
    </AreYouSure>
  </Header>
</template>

<script>
import messages from './lang';

import AreYouSure from '@/components/AreYouSure';
import Header from '@/components/Header';

export default {
  components: {
    AreYouSure,
    Header
  },
  i18n: messages,
  data() {
    return {
      showAreYouSure: false
    };
  },
  computed: {
    story() {
      return this.$store.state.news.active;
    },
    picture() {
      if (this.story) {
        return this.story.detail_picture;
      }
      return null;
    },
    badge() {
      return {
        name: this.story.category.name,
        route: {
          name: 'news.category',
          params: {
            category: this.story.category.id
          }
        }
      };
    },
    toolbar() {
      const buttons = [];
      if (this.$can('update', this.story)) {
        buttons.push({
          icon: 'fas fa-edit',
          route: {
            name: 'news.update',
            params: {
              id: this.story.id
            }
          }
        });
      }
      if (this.$can('delete', this.story)) {
        buttons.push({
          icon: 'fas fa-trash',
          method: this.showModal
        });
      }
      return buttons;
    }
  },
  methods: {
    deleteStory() {
      this.showAreYouSure = false;
      this.$store.dispatch('news/delete', {
        story: this.story
      }).then(() => {
        this.$router.push({ name: 'news.browse' });
      });
    },
    showModal() {
      this.showAreYouSure = true;
    }
  }
};
</script>
