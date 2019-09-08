<template>
  <div class="teamtype-grid">
    <div class="teamtype-header">
      <h4 style="margin: 0px;">{{ type.name }}</h4>
      <p class="kwai-text-meta kwai-text-truncated">
        {{ type.remark }}
      </p>
    </div>
    <div class="teamtype-tools">
      <router-link
        v-if="$can('update', type)"
        class="kwai-icon-button"
        :to="{ name : 'team_types.update', params : { id : type.id } }"
      >
        <i class="fas fa-edit"></i>
      </router-link>
      <router-link
        class="kwai-icon-button"
        :to="{ name: 'team_types.read', params: { id : type.id} }"
      >
        <i class="fas fa-search"></i>
      </router-link>
    </div>
    <div class="teamtype-content">
      <dl class="kwai-attributes">
        <dt>{{ $t('age') }}</dt>
        <dd>{{ type.start_age }} - {{ type.end_age }}</dd>
        <dt>{{ $t('gender') }}</dt>
        <dd>{{ gender }}</dd>
        <dt>{{ $t('competition') }}</dt>
        <dd>
          <i
            v-if="type.competition"
            class="fas fa-check"
          >
          </i>
          <i
            v-else
            class="fas fa-times"
            style="color: var(--kwai-color-danger);"
          >
          </i>
        </dd>
        <dt>{{ $t('active') }}</dt>
        <dd>
          <i
            v-if="type.active"
            class="fas fa-check"
          >
          </i>
          <i
            v-else
            class="fas fa-times"
            style="color: var(--kwai-color-danger);"
          >
          </i>
        </dd>
      </dl>
    </div>
    <div
      class="teamtype-footer kwai-text-meta"
      style="display: flex; flex-wrap: wrap;"
    >
      <div>
        <strong>Aangemaakt:</strong> {{ type.localCreatedAt }}&nbsp;&nbsp;
      </div>
      <div>
        <strong>Laatst gewijzigd:</strong> {{ type.localUpdatedAt }}
      </div>
    </div>
  </div>
</template>

<style lang=scss>
  @import "@/site/scss/_mq.scss";

  .teamtype-header {
    grid-area: type-header;
    padding-left: 20px;
    padding-top: 20px;
    margin-bottom: 10px;

    p {
      margin: 0px;
      margin-bottom: 10px;
      padding-left:10px;
    }
  }

  .teamtype-tools {
    grid-area: type-tools;
    justify-self: right;
  }

  .teamtype-footer {
    grid-area: type-footer;
    padding-left: 20px;
    align-self: center;
  }

  .teamtype-content {
    grid-area: type-content;
    padding: 20px;
    border-bottom: 1px solid var(--kwai-color-muted);
    border-top: 1px solid var(--kwai-color-muted);
  }

  .teamtype-grid {
        display: grid;
        grid-template-columns: 80% 1fr;
        grid-template-rows: auto 1fr auto;
        grid-template-areas:
            "type-header type-header"
            "type-content type-content"
            "type-footer type-tools"
        ;
        justify-content: space-between;
        box-shadow: 0px 0px 20px 0 rgba(100, 100, 100, 0.3);
        height: 100%;
  }
</style>

<script>
import messages from './lang';

export default {
  props: {
    type: {
      type: Object,
      required: true
    }
  },
  i18n: messages,
  computed: {
    gender() {
      var gender = this.type.gender;
      if (gender === 0) {
        return this.$t('no_restriction');
      } else if (gender === 1) {
        return this.$t('male');
      } else {
        return this.$t('female');
      }
    },
  }
};
</script>
