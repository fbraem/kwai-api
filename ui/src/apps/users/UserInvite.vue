<template>
  <div uk-grid>
    <div>
      <div
        class="uk-width-1-1 uk-alert-primary"
        uk-alert
      >
        {{ $t('invite_intro') }}
      </div>
    </div>
    <div class="uk-width-1-1">
      <KwaiForm
        :form="form"
        :error="error"
        :save="$t('save')"
        @submit="submit"
      >
        <KwaiField
          name="first_name"
          :label="$t('form.first_name.label')"
        >
          <KwaiInput-text :placeholder="$t('form.first_name.placeholder')" />
        </KwaiField>
        <KwaiField
          name="last_name"
          :label="$t('form.last_name.label')"
        >
          <KwaiInputText :placeholder="$t('form.last_name.placeholder')" />
        </KwaiField>
        <KwaiField
          name="email"
          :label="$t('form.email.label')"
        >
          <KwaiEmail :placeholder="$t('form.email.placeholder')" />
        </KwaiField>
      </KwaiForm>
    </div>
  </div>
</template>

<script>
import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiEmail from '@/components/forms/KwaiEmail.vue';

import makeForm, { makeField, isEmail, notEmpty } from '@/js/Form';
const makeInviteForm = (fields) => {
  const writeForm = (invitation) => {
    fields.first_name.value = invitation.first_name;
    fields.last_name.value = invitation.last_name;
    fields.email.value = invitation.email;
  };
  const readForm = (invitation) => {
    invitation.first_name = fields.first_name.value;
    invitation.last_name = fields.last_name.value;
    invitation.email = fields.email.value;
  };

  return { ...makeForm(fields), writeForm, readForm };
};

import messages from './lang';

import Invitation from '@/models/users/Invitation';

export default {
  i18n: messages,
  components: {
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiEmail
  },
  data() {
    return {
      form: makeInviteForm({
        first_name: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
          ]
        }),
        last_name: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
          ]
        }),
        email: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
            {
              v: isEmail,
              error: this.$t('form.email.invalid')
            },
          ]
        }),
      })
    };
  },
  computed: {
    error() {
      return this.$store.state.user.error;
    }
  },
  methods: {
    submit() {
      this.form.clearErrors();
      var invitation = new Invitation();
      this.form.readForm(invitation);
      this.$store.dispatch('user/invitation/invite', invitation)
        .then((invitation) => {
          this.$router.push({
            name: 'home'
          });
        })
        .catch((error) => {
          console.log(this.$config);
          console.log(error);
        });
    }
  }
};
</script>
