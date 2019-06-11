<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <KwaiForm
        :form="form"
        :error="error"
        :save="$t('save')"
        @submit="submit"
      >
        <KwaiField
          name="name"
          :label="$t('rules.form.name.label')"
        >
          <KwaiInputText :placeholder="$t('rules.form.name.placeholder')" />
        </KwaiField>
        <KwaiField
          name="remark"
          :label="$t('rules.form.remark.label')"
        >
          <KwaiTextarea
            :rows="5"
            :placeholder="$t('rules.form.remark.placeholder')"
          />
        </KwaiField>
        <KwaiField
          name="rules"
          :label="$t('rules.form.rules.label')"
        >
          <multiselect
            :options="rules"
            group-values="actions"
            group-label="subject"
            label="name"
            track-by="id"
            :multiple="true"
            :close-on-select="false"
            :selectLabel="$t('rules.form.rules.selectLabel')"
            :deselectLabel="$t('rules.form.rules.deselectLabel')"
          />
        </KwaiField>
      </KwaiForm>
    </div>
  </div>
</template>

<script>
import RuleGroup from '@/models/users/RuleGroup';

import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText';
import KwaiTextarea from '@/components/forms/KwaiTextarea';
import Multiselect from '@/components/forms/MultiSelect.vue';

import makeForm, { makeField, notEmpty } from '@/js/Form';
const makeRuleGroupForm = (fields, validations) => {
  const writeForm = (rule_group) => {
    fields.name.value = rule_group.name;
    fields.remark.value = rule_group.remark;
    fields.rules.value = rule_group.rules || [];
  };

  const readForm = (rule_group) => {
    rule_group.name = fields.name.value;
    rule_group.remark = fields.remark.value;
    rule_group.rules = fields.rules.value;
  };
  return { ...makeForm(fields, validations), writeForm, readForm };
};

import messages from './lang';

export default {
  components: {
    KwaiForm, KwaiField, KwaiInputText, KwaiTextarea, Multiselect
  },
  i18n: messages,
  data() {
    var rule_group = new RuleGroup();
    rule_group.rules = [];
    return {
      rule_group,
      form: makeRuleGroupForm({
        name: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('rules.form.name.required'),
            },
          ]
        }),
        remark: makeField(),
        rules: makeField()
      })
    };
  },
  async created() {
    await this.$store.dispatch('user/rule/browseRules');
  },
  computed: {
    error() {
      return this.$store.state.user.rule.error;
    },
    rules() {
      var options = [];
      if (this.$store.state.user.rule.rules) {
        for (let rule of this.$store.state.user.rule.rules) {
          var option = options.find((r) => {
            return r.subject === rule.subject.name;
          });
          if (!option) {
            option = {
              subject: rule.subject.name,
              actions: []
            };
            options.push(option);
          }
          option.actions.push({
            id: rule.id,
            action: rule.action.name,
            name: rule.name
          });
        }
      }
      return options;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      if (to.params.id) await vm.fetchData(to.params.id);
      next();
    });
  },
  methods: {
    async fetchData(id) {
      this.rule_group
        = await this.$store.dispatch('user/rule/read', {
          id: id
        });
      this.form.writeForm(this.rule_group);
    },
    submit() {
      this.form.clearErrors();
      this.form.readForm(this.rule_group);
      this.$store.dispatch('user/rule/save', this.rule_group)
        .then((newRuleGroup) => {
          this.$router.push({
            name: 'user.rule.read',
            params: { id: newRuleGroup.id }
          });
        });
    }
  }
};
</script>
