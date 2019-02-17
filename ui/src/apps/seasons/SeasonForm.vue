<template>
  <!-- eslint-disable max-len -->
  <div>
    <div uk-grid>
      <div class="uk-width-1-1">
        <form class="uk-form-stacked">
          <field name="name" :label="$t('form.season.name.label')">
            <uikit-input-text :placeholder="$t('form.season.name.placeholder')" />
          </field>
          <field name="start_date" :label="$t('form.season.start_date.label')">
            <uikit-input-text :placeholder="$t('form.season.start_date.placeholder')" />
          </field>
          <field name="end_date" :label="$t('form.season.end_date.label')">
            <uikit-input-text :placeholder="$t('form.season.end_date.placeholder')" />
          </field>
          <field name="remark" :label="$t('form.season.remark.label')">
            <uikit-textarea :placeholder="$t('form.season.remark.placeholder')" />
          </field>
        </form>
      </div>
      <div uk-grid class="uk-width-1-1">
        <div class="uk-width-expand">
        </div>
        <div class="uk-width-auto">
          <button class="uk-button uk-button-primary" :disabled="!$valid" @click="submit">
            <i class="fas fa-save"></i>&nbsp; {{ $t('save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Season from '@/models/Season';

import seasonStore from '@/stores/seasons';
import registerModule from '@/stores/mixin';

import SeasonForm from './SeasonForm';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';

import messages from './lang';

export default {
  i18n: messages,
  components: {
    Field,
    UikitInputText,
    UikitTextarea
  },
  mixins: [
    SeasonForm,
    registerModule({ season: seasonStore }),
  ],
  data() {
    return {
      season: new Season()
    };
  },
  computed: {
    creating() {
      return this.season != null && this.season.id == null;
    },
    error() {
      return this.$store.state.season.error;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    if (to.params.id) await this.fetchData(to.params);
    next();
  },
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404) {
          // this.error = err.response.statusText;
        } else {
          // TODO: check if we can get here ...
          console.log(nv);
        }
      }
    }
  },
  methods: {
    async fetchData(params) {
      this.season = await this.$store.dispatch('season/read', {
        id: params.id
      });
      this.writeForm(this.season);
    },
    async submit() {
      this.clearErrors();
      this.readForm(this.season);
      try {
        this.season = await this.$store.dispatch('season/save', this.season);
        this.$router.push({
          name: 'seasons.read',
          params: {
            id: this.season.id
          }
        });
      } catch (error) {
        console.log(error);
      };
    }
  }
};
</script>
