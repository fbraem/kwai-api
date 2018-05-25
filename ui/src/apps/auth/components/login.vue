<template>
    <div>
        <v-btn v-if="$base.isAllowed('login')" @click="showLogin = true" icon>
            <v-icon>lock</v-icon>
        </v-btn>
        <v-menu v-else offset-y right>
            <a class="uk-button uk-button-default">
                <fa-icon name="user" />
            </a>
            <v-list>
                <v-list-tile @click="logout">
                    <v-list-tile-title>{{ $t('logout') }}</v-list-tile-title>
                </v-list-tile>
            </v-list>
        </v-menu>
        <v-dialog v-model="showLogin" max-width="450">
            <v-card>
                <v-toolbar dark color="red">
                    <v-toolbar-title>Login</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click.native="closeLogin">
                        <v-icon>fa-times</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-title>
                    <div>
                        {{ $t('welcome') }}
                    </div>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-layout wrap>
                            <v-flex xs12>
                                <v-text-field name="email"
                                    :label="$t('email.label')"
                                    v-model="email"
                                    :error-messages="emailErrors"
                                    @input="$v.email.$touch()"
                                    append-icon="mail"
                                    type="email"
                                    required
                                    autofocus>
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field
                                  name="password"
                                  :label="$t('password.label')"
                                  v-model="password"
                                  :error-messages="passwordErrors"
                                  @input="$v.password.$touch()"
                                  :append-icon="hidePassword ? 'visibility' : 'visibility_off'"
                                  :append-icon-cb="() => (hidePassword = !hidePassword)"
                                  :type="hidePassword ? 'password' : 'text'"
                                  required
                                ></v-text-field>
                            </v-flex>
                            <v-alert color="error" v-model="showUnauthorized">
                              {{ $t('not_authorized') }}
                            </v-alert>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-btn :disabled="$v.$invalid" flat @click="submit">{{ $t('submit') }}</v-btn>
                    <v-btn flat @click="clear">{{ $t('clear') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import 'vue-awesome/icons/user';

    import Model from '@/js/model';

    import { required, email } from 'vuelidate/lib/validators';

    var initError = function() {
      return {
        email : [],
        password : []
      }
    }

    import messages from '../lang/Login';

    export default {
        i18n : {
            messages
        },
        components : {
        },
        data() {
            return {
                showLogin : false,
                showLogout : false,
                showUnauthorized : false,
                email : '',
                password : '',
                hidePassword : true,
                errors : initError()
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
            error() {
                return this.$store.state.status.error;
            },
            emailErrors() {
                const errors = [];
                if (! this.$v.email.$dirty) return errors;
                ! this.$v.email.required && errors.push(this.$t('email.required'));
                ! this.$v.email.email && errors.push(this.$t('email.invalid'));
                return errors;
            },
            passwordErrors() {
                const errors = [];
                if (! this.$v.password.$dirty) return errors;
                ! this.$v.password.required && errors.push(this.$t('password.required'));
                return errors;
            }
        },
        watch : {
            error(nv) {
                if (nv) {
                    if ( nv.response.status == 422 ) {
                        nv.response.data.errors.forEach((item, index) => {
                            if ( item.source && item.source.pointer ) {
                                var attr = item.source.pointer.split('/').pop();
                                this.errors[attr].push(item.title);
                            }
                        });
                    }
                    else if ( nv.response.status == 404 ) {
                        this.showUnauthorized = true;
                    }
                    else if ( nv.response.status == 401 ) {
                      this.showUnauthorized = true;
                  } else {
                      //TODO: check if we can get here ...
                      console.log(nv);
                  }
                }
            }
        },
        methods : {
            clear() {
                this.$v.$reset();
                this.email = "";
                this.password = "";
                this.showUnauthorized = false;
            },
            submit() {
                this.showUnauthorized = false;
                this.errors = initError();

                var user = new Model('users');
                user.addAttribute('email', this.email);
                user.addAttribute('password', this.password);

                this.$store.dispatch('login', user.serialize())
                    .then(() => {
                        this.showLogin = false;
                    }).catch(() => {
                    });
            },
            logout() {
                this.$store.dispatch('logout')
                    .then(() => {
                        //this.$router.go('/');
                    });
            },
            closeLogin() {
                this.clear();
                this.showLogin = false;
            }
        }
    };
</script>
