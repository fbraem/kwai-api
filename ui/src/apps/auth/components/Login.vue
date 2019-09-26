<template>
  <!-- eslint-disable max-len -->
  <div>
    <Modal
      v-show="showLogin"
      @close="showLogin = false;"
    >
      <template slot="header">
        <h3>
          {{ $t('login') }}
        </h3>
      </template>
      <template slot="default">
        <Alert
          v-if="error"
          type="danger"
        >
          {{error}}
        </Alert>
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
      </template>
    </Modal>
    <div v-if="isLoggedIn">
      <router-link
        class="primary:kwai-icon-button"
        :to="{ name: 'users.read', params: { id: activeUser.id } }"
        >
        <i class="fas fa-user"></i>
      </router-link>
      <a class="primary:kwai-icon-button" @click="logout">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
    <div v-else>
      <a class="primary:kwai-icon-button" @click="showLogin = true">
        <i class="fas fa-lock"></i>
      </a>
    </div>
  </div>
</template>

<script>
import User from '@/models/users/User';

import makeForm, { makeField, notEmpty, isEmail } from '@/js/Form.js';

import KwaiForm from '@/components/forms/KwaiForm.vue';
import KwaiField from '@/components/forms/KwaiField.vue';
import KwaiEmail from '@/components/forms/KwaiEmail.vue';
import KwaiPassword from '@/components/forms/KwaiPassword.vue';
import Modal from '@/components/Modal';
import Alert from '@/components/Alert';

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
    KwaiPassword,
    Modal,
    Alert
  },
  data() {
    return {
      user: new User(),
      showLogin: false,
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
    },
    activeUser() {
      return this.$store.state.auth.user;
    }
  },
  methods: {
    clear() {
      this.form.clear();
      this.form.clearErrors();
    },
    submit() {
      this.error = null;
      this.form.readForm(this.user);
      this.$store.dispatch('auth/login', this.user)
        .then(() => {
          this.clear();
          this.showLogin = false;
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
  }
};
</script>
