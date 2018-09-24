<template>
    <div>
        <div id="login-form" uk-modal ref="dialog">
            <div class="uk-modal-dialog uk-modal-body">
                <h2 class="uk-modal-title">{{ $t('login') }}</h2>
                <form>
                    <uikit-email v-model="email" :validator="$v.email" :errors="emailErrors" id="email" :placeholder="$t('email.placeholder')">
                        {{ $t('email.label') }}:
                    </uikit-email>
                    <uikit-password v-model="password" :validator="$v.password" :errors="passwordErrors" id="password" :placeholder="$t('password.placeholder')">
                        {{ $t('password.label') }}:
                    </uikit-password>
                    <div v-if="error" uk-alert class="uk-alert-danger">
                        {{ error }}
                    </div>
                    <p class="uk-text-right">
                        <button class="uk-button uk-button-default uk-modal-close" type="button">{{ $t('cancel') }}</button>
                        <button class="uk-button uk-button-primary" type="button" :disabled="$v.$invalid" @click="submit">{{ $t('login') }}</button>
                    </p>
                </form>
            </div>
        </div>
        <div v-if="$base.isAllowed('login')" class="uk-inline">
            <a class="uk-icon-button" @click="login">
                <fa-icon name="lock" />
            </a>
        </div>
        <div v-else class="uk-inline">
            <a class="uk-icon-button">
                <fa-icon name="user" />
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
    import 'vue-awesome/icons/user';
    import 'vue-awesome/icons/lock';

    import User from '@/models/User';

    import UIkit from 'uikit';

    import { validationMixin } from 'vuelidate';
    import { required, email } from 'vuelidate/lib/validators';

    var initError = function() {
      return {
        email : [],
        password : []
      }
    }

    import UikitEmail from '@/components/uikit/Email.vue';
    import UikitPassword from '@/components/uikit/Password.vue';

    import messages from '../lang';

    export default {
        i18n : messages,
        components : {
            UikitEmail,
            UikitPassword
        },
        mixins: [
            validationMixin
        ],
        data() {
            return {
                email : '',
                password : '',
                hidePassword : true,
                errors : initError(),
                error : null
            };
        },
        validations : {
            email : {
                required,
                email
            },
            password : {
                required
            }
        },
        computed : {
            emailErrors() {
                const errors = [... this.errors.email];
                if (! this.$v.email.$dirty) return errors;
                ! this.$v.email.required && errors.push(this.$t('email.required'));
                ! this.$v.email.email && errors.push(this.$t('email.invalid'));
                return errors;
            },
            passwordErrors() {
                const errors = [... this.errors.password];
                if (! this.$v.password.$dirty) return errors;
                ! this.$v.password.required && errors.push(this.$t('password.required'));
                return errors;
            }
        },
        methods : {
            clear() {
                this.$v.$reset();
                this.email = "";
                this.password = "";
            },
            login() {
                var modal = UIkit.modal(this.$refs.dialog);
                modal.show();
            },
            submit() {
                this.errors = initError();
                this.error = null;

                var user = new User();
                user.email = this.email;
                user.password = this.password;

                this.$store.dispatch('login', user)
                    .then(() => {
                        this.clear();
                        var modal = UIkit.modal(this.$refs.dialog);
                        modal.hide();
                    }).catch((e) => {
                        if (e.response.data.error == 'invalid_credentials') {
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
