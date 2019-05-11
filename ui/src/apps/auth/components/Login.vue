<template>
  <!-- eslint-disable max-len -->
  <div>
    <div
      id="login-form"
      uk-modal
      ref="dialog"
    >
      <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">
          {{ $t('login') }}
        </h2>
        <div
          v-if="error"
          class="uk-alert-danger"
          uk-alert
        >
          {{error}}
        </div>
        <KwaiForm
          :form="form"
          @submit="submit"
          :save="$t('login')"
          icon="fas fa-unlock"
        >
          <KwaiField
            name="login_email"
            :label="$t('email.label')"
          >
            <KwaiEmail :placeholder="$t('email.placeholder')" />
          </KwaiField>
          <KwaiField
            name="login_password"
            :label="$t('password.label')"
          >
            <KwaiPassword :placeholder="$t('password.placeholder')" />
          </KwaiField>
        </KwaiForm>
      </div>
    </div>
    <div
      v-if="isLoggedIn"
      class="uk-inline"
    >
      <a class="uk-icon-button uk-link-reset">
        <i class="fas fa-user"></i>
      </a>
      <div uk-dropdown="mode: click">
        <ul class="uk-nav uk-dropdown-nav">
          <li>
            <a href="#">{{ $t('user') }}</a>
          </li>
          <li>
            <a href="#" @click="logout">{{ $t('logout') }}</a>
          </li>
        </ul>
      </div>
    </div>
    <div
      v-else
      class="uk-inline"
    >
      <a class="uk-icon-button uk-link-reset" @click="login">
        <i class="fas fa-lock"></i>
      </a>
    </div>
  </div>
</template>

<script>
import User from '@/models/users/User';

import UIkit from 'uikit';

import makeForm, { makeField, notEmpty, isEmail } from '@/js/Form.js';

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiEmail from '@/components/forms/KwaiEmail.vue';
import KwaiPassword from '@/components/forms/KwaiPassword.vue';

const makeLoginForm = (fields) => {
  const writeForm = (user) => {
    fields.login_email.value = user.email;
    fields.login_password.value = user.password;
  };
  const readForm = (user) => {
    user.email = fields.login_email.value;
    user.password = fields.login_password.value;
  };
  const clear = () => {
    fields.login_email.value = '';
    fields.login_password.value = '';
  };
  return { ...makeForm(fields), writeForm, readForm, clear };
};

import messages from '../lang';

export default {
  i18n: messages,
  components: {
    KwaiForm,
    KwaiField,
    KwaiEmail,
    KwaiPassword
  },
  data() {
    return {
      user: new User(),
      modal: null,
      error: null,
      form: makeLoginForm({
        login_email: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('email.required')
            },
            {
              v: isEmail,
              error: this.$t('email.invalid')
            },
          ]
        }),
        login_password: makeField({
          required: true,
          validators: [
            {
              v: notEmpty,
              error: this.$t('password.required')
            },
          ]
        })
      })
    };
  },
  computed: {
    isLoggedIn() {
      return this.$store.getters['auth/authenticated'];
    }
  },
  methods: {
    clear() {
      this.form.clear();
      this.form.clearErrors();
    },
    login() {
      if (!this.modal) {
        this.modal = UIkit.modal(document.getElementById('login-form'));
      }
      this.modal.show();
    },
    submit() {
      this.error = null;
      this.form.readForm(this.user);
      this.$store.dispatch('auth/login', this.user)
        .then(() => {
          this.clear();
          this.modal.hide();
        }).catch((e) => {
          this.clear();
          if (e.response && e.response.data.error === 'invalid_credentials') {
            this.error = this.$t('invalid_credentials');
          } else {
            console.log(e);
          }
        });
    },
    logout() {
      this.$store.dispatch('auth/logout')
        .then(() => {
          this.$router.push('/');
        });
    },
    closeLogin() {
      this.clear();
    }
  }
};
</script>
