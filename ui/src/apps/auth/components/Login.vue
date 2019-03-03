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
        <KwaiForm :form="form">
          <KwaiField
            name="email"
            :label="$t('email.label')"
          >
            <KwaiEmail :placeholder="$t('email.placeholder')" />
          </KwaiField>
          <KwaiField
            name="password"
            :label="$t('password.label')"
          >
            <KwaiPassword :placeholder="$t('password.placeholder')" />
          </KwaiField>
          <p class="uk-text-right">
            <button
              class="uk-button uk-button-default uk-modal-close"
              type="button"
            >
              {{ $t('cancel') }}
            </button>
            <button
              class="uk-button uk-button-primary"
              type="button"
              :disabled="!form.$valid"
              @click="submit"
            >
              {{ $t('login') }}
            </button>
          </p>
        </KwaiForm>
      </div>
    </div>
    <div v-if="isLoggedIn" class="uk-inline">
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
    <div v-else class="uk-inline">
      <a class="uk-icon-button uk-link-reset" @click="login">
        <i class="fas fa-lock"></i>
      </a>
    </div>
  </div>
</template>

<script>
import User from '@/models/User';

import UIkit from 'uikit';

import { notEmpty, isEmail } from '@/js/VueForm';
import makeForm from '@/js/Form.js';

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiEmail from '@/components/forms/KwaiEmail.vue';
import KwaiPassword from '@/components/forms/KwaiPassword.vue';

const makeLoginForm = (fields) => {
  const writeForm = (user) => {
    fields.email.value = user.email;
    fields.password.value = user.password;
  };
  const readForm = (user) => {
    user.email = fields.email.value;
    user.password = fields.password.value;
  };
  return { ...makeForm(fields), writeForm, readForm };
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
      error: null,
      form: makeLoginForm({
        email: {
          required: true,
          value: '',
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
        },
        password: {
          required: true,
          value: '',
          validators: [
            {
              v: notEmpty,
              error: this.$t('password.required')
            },
          ]
        }
      })
    };
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.global.user
        && this.$store.state.global.user.authenticated;
    }
  },
  methods: {
    clear() {
      this.user.email = '';
      this.user.password = '';
    },
    login() {
      var modal = UIkit.modal(this.$refs.dialog);
      modal.show();
    },
    submit() {
      this.error = null;
      this.form.clearErrors();
      this.form.readForm(this.user);
      this.$store.dispatch('login', this.user)
        .then(() => {
          this.clear();
          var modal = UIkit.modal(this.$refs.dialog);
          modal.hide();
        }).catch((e) => {
          if (e.response.data.error === 'invalid_credentials') {
            this.error = this.$t('invalid_credentials');
          } else {
            console.log(e.response.data);
          }
        });
    },
    logout() {
      this.$store.dispatch('logout')
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
