<template>
  <!-- eslint-disable max-len -->
  <div uk-grid class="uk-margin-left uk-grid-small uk-child-width-1-1">
    <div>
      <h4 class="uk-heading-line uk-text-bold">
        <span>{{ $t('overview') }}</span>
      </h4>
    </div>
    <div>
      <ul class="uk-list uk-list-divider">
        <li v-for="(category, index) in categories" :key="index">
          <div class="uk-flex uk-flex-middle uk-inline" uk-grid>
            <div v-if="category.icon_picture" class="uk-width-auto">
              <img :src="category.icon_picture"
                style="color:#c61c18;height:32px;"
                width="40" height="32" uk-svg />
            </div>
            <div class="uk-width-expand">
              <router-link :to="{ name : 'categories.read', params : { id : category.id }}" class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent" />
              <h4 class="uk-h4 uk-margin-remove">{{ category.name }}</h4>
              <div class="uk-text-meta">
                {{ category.short_description }}
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import messages from '../lang';

/**
 * Component for showing a list of categories
 */
export default {
  props: {
    categories: {
      type: Array,
      required: true
    }
  },
  i18n: messages
};
</script>
