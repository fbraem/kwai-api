<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <form class="uk-form-stacked">
        <field name="name" :label="$t('form.team_type.name.label')">
          <uikit-input-text :placeholder="$t('form.team_type.name.placeholder')" />
        </field>
        <field name="start_age" :label="$t('form.team_type.start_age.label')">
          <uikit-input-text :placeholder="$t('form.team_type.start_age.placeholder')" />
        </field>
        <field name="end_age" :label="$t('form.team_type.end_age.label')">
          <uikit-input-text :placeholder="$t('form.team_type.end_age.placeholder')" />
        </field>
        <field name="gender" :label="$t('form.team_type.gender.label')">
          <uikit-select :items="genders" />
        </field>
        <field name="active" :label="$t('form.team_type.active.label')">
          <uikit-checkbox />
        </field>
        <field name="competition" :label="$t('form.team_type.competition.label')">
          <uikit-checkbox />
        </field>
        <field name="remark" :label="$t('form.team_type.remark.label')">
          <uikit-textarea :placeholder="$t('form.team_type.remark.placeholder')" />
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
</template>

<script>
import teamTypeStore from '@/stores/team_types';
import registerModule from '@/stores/mixin';

import TeamType from '@/models/TeamType';

import TeamTypeForm from './TeamTypeForm';
import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSelect from '@/components/forms/Select.vue';
import UikitCheckbox from '@/components/forms/Checkbox.vue';

import messages from './lang';

export default {
  components: {
    Field, UikitInputText, UikitTextarea, UikitSelect, UikitCheckbox
  },
  i18n: messages,
  mixins: [
    TeamTypeForm,
    registerModule({
      teamType: teamTypeStore
    }),
  ],
  data() {
    return {
      teamType: new TeamType(),
      genders: [
        {
          value: 0,
          text: this.$t('no_restriction')
        },
        {
          value: 1,
          text: this.$t('male')
        },
        {
          value: 2,
          text: this.$t('female')
        },
      ]
    };
  },
  computed: {
    creating() {
      return this.teamType != null && this.teamType.id == null;
    },
    error() {
      return this.$store.state.teamType.error;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params);
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
      this.teamType = await this.$store.dispatch('teamType/read', {
        id: params.id
      });
      this.writeForm(this.teamType);
    },
    submit() {
      this.clearErrors();
      this.readForm(this.teamType);
      this.$store.dispatch('teamType/save', this.teamType)
        .then((newType) => {
          this.$router.push({
            name: 'team_types.read',
            params: {
              id: newType.id
            }
          });
        }).catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
