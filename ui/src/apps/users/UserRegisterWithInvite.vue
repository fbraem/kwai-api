<template>
  <!-- eslint-disable max-len -->
  <div>
    <div
      v-if="invitation"
      class="uk-container"
    >
      <div
        v-if="invitation.isExpired"
        class="uk-alert-danger"
        uk-alert
      >
        <p>
          {{ $t('invitation.expired') }}
        </p>
      </div>
      <div v-else>
        <div
          class="uk-width-1-1"
          uk-grid
        >
          <div
            class="uk-width-1-1 uk-alert-primary"
            uk-alert
          >
            {{ $t('invitation.intro') }}
          </div>
          <div class="uk-width-1-1">
            <KwaiForm
              :form="form"
              :error="error"
            >
              <KwaiField
                name="first_name"
                :label="$t('form.first_name.label')"
              >
                <KwaiInputText :placeholder="$t('form.first_name.placeholder')"/>
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
              <KwaiField
                name="password"
                :label="$t('form.password.label')"
              >
                <KwaiPassword :placeholder="$t('form.password.placeholder')" />
              </KwaiField>
              <KwaiField
                name="retype_password"
                :label="$t('form.retype_password.label')"
              >
                <KwaiPassword :placeholder="$t('form.retype_password.placeholder')" />
              </KwaiField>
            </KwaiForm>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <div
        v-if="error && error.response.status == 404"
        class="uk-alert-danger"
        uk-alert
      >
        {{ $t('invitation.not_found') }}
      </div>
    </div>
  </div>
</template>

<script>
import KwaiForm from '@/components/forms/KwaiForm';
import KwaiField from '@/components/forms/KwaiField';
import KwaiInputText from '@/components/forms/KwaiInputText.vue';
import KwaiPassword from '@/components/forms/KwaiPassword.vue';
import KwaiEmail from '@/components/forms/KwaiEmail.vue';

import passwordComplexity from '@/js/passwordComplexity';

import makeForm, { makeField, notEmpty, minLength } from '@/js/Form';
const makeRegisterWithInviteForm = (fields, validations) => {
  const writeForm = (user) => {
  };
  const readForm = (user) => {
    user.first_name = fields.user.first_name.value;
    user.last_name = fields.user.last_name.value;
    user.email = fields.user.email.value;
    user.password = fields.user.password.value;
  };

  return { ...makeForm(fields, validations), writeForm, readForm };
};

import messages from './lang';

import User from '@/models/User';

export default {
  i18n: messages,
  components: {
    KwaiForm,
    KwaiField,
    KwaiInputText,
    KwaiPassword,
    KwaiEmail
  },
  data() {
    return {
      invitation: null,
      form: makeRegisterWithInviteForm({
        first_name: makeField({
          required: true,
          validations: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
          ]
        }),
        last_name: makeField({
          required: true,
          validations: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
          ]
        }),
        email: makeField({
          required: true,
          validations: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
          ]
        }),
        password: makeField({
          required: true,
          validations: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
            {
              v: minLength,
              params: {
                min: 8
              },
              error: this.$t('form.password.complex')
            },
            {
              v: passwordComplexity,
              error: this.$t('form.password.complex')
            },
          ]
        }),
        retype_password: makeField({
          required: true,
          validations: [
            {
              v: notEmpty,
              error: this.$t('required')
            },
          ]
        }),
      },
      [
        ({password, retype_password}) => {
          if (password.value === retype_password.value) {
            retype_password.errors.splice(0, retype_password.errors.length);
            return true;
          }
          retype_password.errors.push(this.$t('form.retype_password.sameas'));
          return false;
        },
      ]),
    };
  },
  computed: {
    error() {
      return this.$store.state.user.error;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(async(vm) => {
      await vm.fetchData(to.params.token);
      next();
    });
  },
  async beforeRouteUpdate(to, from, next) {
    await this.fetchData(to.params.token);
    next();
  },
  methods: {
    fetchData(token) {
      this.$store.dispatch('user/readInvitationByToken',
        { token }
      ).then((invitation) => {
        this.invitation = invitation;
      }).catch((error) => {
        this.error = error;
      });
    },
    submit() {
      var user = new User();
      this.form.clearErrors();
      this.form.readForm(user);
      this.$store.dispatch('user/createWithToken', {
        user,
        token: this.$route.params.token
      }).then((user) => {
        this.$router.push({
          name: 'home'
        });
      });
    }
  }
};
</script>
