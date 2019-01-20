<template>
  <!-- eslint-disable max-len -->
  <div>
    <div id="login-form" uk-modal ref="dialog">
      <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">{{ $t('login') }}</h2>
        <div v-if="error" class="uk-alert-danger" uk-alert>
          {{error}}
        </div>
        <form class="uk-form-stacked">
          <field name="email" :label="$t('email.label')">
            <uikit-email :placeholder="$t('email.placeholder')" />
          </field>
          <field name="password" :label="$t('password.label')">
            <uikit-password :placeholder="$t('password.placeholder')" />
          </field>
          <p class="uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">{{ $t('cancel') }}</button>
            <button class="uk-button uk-button-primary" type="button" :disabled="!$valid" @click="submit">{{ $t('login') }}</button>
          </p>
        </form>
      </div>
    </div>
    <div v-if="$base.isAllowed('login')" class="uk-inline">
      <a class="uk-icon-button uk-link-reset" @click="login">
        <i class="fas fa-lock"></i>
      </a>
    </div>
    <div v-else class="uk-inline">
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
  </div>
</template>

<script>
import User from '@/models/User';

import UIkit from 'uikit';

import LoginForm from './Login';
import Field from '@/components/forms/Field.vue';
import UikitEmail from '@/components/forms/Email.vue';
import UikitPassword from '@/components/forms/Password.vue';

import messages from '../lang';

export default {
  i18n: messages,
  components: {
    Field,
    UikitEmail,
    UikitPassword
  },
  mixins: [
    LoginForm,
  ],
  data() {
    return {
      user: new User(),
      error: null
    };
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
      this.readForm(this.user);
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
