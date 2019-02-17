<template>
  <!-- eslint-disable max-len -->
  <div uk-grid>
    <div class="uk-width-1-1">
      <form class="uk-form-stacked">
        <field v-if="creating" name="member">
          <AutoComplete :placeholder="$t('training.coaches.form.member.placeholder')"
            :items="members"
            :stringResult="(value) => { return value.person.name }">
            <template slot-scope="row">
              {{ row.result.license }} - {{ row.result.person.name }}
            </template>
            <span slot="empty">
              {{ $t('training.coaches.form.member.not_found') }}
            </span>
          </AutoComplete>
        </field>
        <div v-else>
          <field name="member">
            <input class="uk-input" type="text" readonly="readonly" v-model="coach.name" />
          </field>
        </div>
        <div uk-grid>
          <div class="uk-width-expand">
            <field name="diploma">
              <uikit-input-text :placeholder="$t('training.coaches.form.diploma.placeholder')" />
            </field>
          </div>
          <div>
            <field name="active">
              <uikit-switch />
            </field>
          </div>
        </div>
        <field name="description">
          <uikit-textarea :rows="5" :placeholder="$t('training.coaches.form.description.placeholder')" />
        </field>
<!--
        <field name="season">
          <uikit-select :items="seasons" />
        </field>
-->
        <field name="remark">
          <uikit-textarea :rows="5" :placeholder="$t('training.coaches.form.remark.placeholder')" />
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
import trainingStore from '@/stores/training';
import coachStore from '@/stores/training/coaches';
import memberStore from '@/stores/members';
import registerModule from '@/stores/mixin';

import TrainingCoach from '@/models/trainings/Coach';

import Field from '@/components/forms/Field.vue';
import UikitInputText from '@/components/forms/InputText.vue';
import UikitTextarea from '@/components/forms/Textarea.vue';
import UikitSwitch from '@/components/forms/Switch.vue';
import AutoComplete from '@/components/forms/AutoComplete.vue';

import messages from './lang';

import CoachForm from './CoachForm';

export default {
  components: {
    Field, UikitInputText, UikitTextarea,
    UikitSwitch, AutoComplete
  },
  mixins: [
    CoachForm,
    registerModule(
      {
        training: trainingStore,
        coach: coachStore,
      },
      {
        member: memberStore
      }
    ),
  ],
  i18n: messages,
  data() {
    return {
      coach: new TrainingCoach(),
      items: []
    };
  },
  computed: {
    creating() {
      return this.coach != null && this.coach.id == null;
    },
    error() {
      return this.$store.getters['training/coach/error'];
    },
    members() {
      return this.$store.state.member.members;
    },
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
  watch: {
    error(nv) {
      if (nv) {
        if (nv.response.status === 422) {
          this.handleErrors(nv.response.data.errors);
        } else if (nv.response.status === 404){
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
      if (params.id) {
        this.coach
          = await this.$store.dispatch('training/coach/read', {
            id: params.id
          });
        this.writeForm(this.coach);
      } else {
        await this.$store.dispatch('member/browse');
      }
    },
    submit() {
      this.clearErrors();
      this.readForm(this.coach);
      this.$store.dispatch('training/coach/save', this.coach)
        .then((newCoach) => {
          this.$router.push({
            name: 'trainings.coaches.read',
            params: { id: newCoach.id }
          });
        });
    }
  }
};
</script>
