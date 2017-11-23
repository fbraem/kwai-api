<template>
    <div>
        <v-alert v-if="installed" color="error" icon="warning" value="true">
            Installation is already done!
        </v-alert>
        <v-layout v-else row wrap>
            <v-flex xs12 sm4 offset-sm4>
                <v-card flat>
                    <v-card-title>
                        <div>
                            <h3 class="headline mb-0"><v-icon>person</v-icon> Install Clubman</h3>
                            <p class="uk-text-meta uk-text-small uk-margin-remove-top">Create the root user for Clubman</p>
                        </div>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-container fluid>
                            <v-layout>
                                <v-flex xs12>
                                    <v-text-field name="email"
                                        label="Email"
                                        v-model="form.email"
                                        :error-messages="emailErrors"
                                        @input="$v.form.email.$touch()"
                                        required
                                        >
                                    </v-text-field>
                                    <v-text-field
                                      name="password"
                                      label="Enter your password"
                                      v-model="form.password"
                                      :error-messages="passwordErrors"
                                      @input="$v.form.password.$touch()"
                                      :append-icon="hidePassword ? 'visibility' : 'visibility_off'"
                                      :append-icon-cb="() => (hidePassword = !hidePassword)"
                                      :type="hidePassword ? 'password' : 'text'"
                                      required
                                    ></v-text-field>
                                    <v-text-field
                                      name="retype"
                                      label="Retype your password"
                                      v-model="form.retype"
                                      :error-messages="retypeErrors"
                                      @input="$v.form.retype.$touch()"
                                      :append-icon="hideRetype ? 'visibility' : 'visibility_off'"
                                      :append-icon-cb="() => (hideRetype = !hideRetype)"
                                      :type="hideRetype ? 'password' : 'text'"
                                      required
                                    ></v-text-field>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs12>
                                    <v-alert v-if="errorText" icon="warning" value="true">
                                        {{ errorText }}
                                    </v-alert>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs12>
                                    <v-btn :disabled="$v.$invalid" @click="click">Install</v-btn>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import Model from '@/js/model';

import { required, minLength, email, sameAs } from 'vuelidate/lib/validators';

var initError = function() {
  return {
    email : [],
    password : [],
    retype: []
  }
}

export default {
    data() {
        return {
            form : {
                email : '',
                password : '',
                retype : ''
            },
            hidePassword : true,
            hideRetype : true,
            errors : initError(),
            errorText : null
        };
    },
    validations : {
        form : {
            email : {
                required,
                email
            },
            password : {
                required,
                minLength: minLength(8)
            },
            retype : {
                sameAsPassword : sameAs('password')
            }
        }
    },
    computed : {
        installed() {
            return this.$store.state.installModule.installed;
        },
        error() {
            return this.$store.state.installModule.status.error
        },
        emailErrors() {
            const errors = [...this.errors.email];
            if (! this.$v.form.email.$dirty) return errors;
            ! this.$v.form.email.required && errors.push('Email is required');
            ! this.$v.form.email.email && errors.push('Email is not valid');
            return errors;
        },
        passwordErrors() {
            const errors = [...this.errors.password];
            if (! this.$v.form.password.$dirty) return errors;
            ! this.$v.form.password.required && errors.push('Password is required');
            ! this.$v.form.password.minLength && errors.push('Password must be at least 8 characters long');
            return errors;
        },
        retypeErrors() {
            const errors = [...this.errors.retype];
            if (! this.$v.form.retype.$dirty) return errors;
            ! this.$v.form.retype.sameAsPassword && errors.push('Passwords are not the same');
            return errors;
        },
    },
    mounted() {
        this.$store.dispatch('installModule/check');
    },
    watch : {
        error(nv) {
            if (nv) {
                this.errorText = null;
                if ( nv.response.status == 422 ) {
                    nv.response.data.errors.forEach((item, index) => {
                        if ( item.source && item.source.pointer ) {
                            var attr = item.source.pointer.split('/').pop();
                            this.errors[attr].push(item.title);
                        }
                    });
                }
                else {
                  this.errorText = nv.response.statusText;
                }
            }
        }
    },
    methods : {
        click() {
            this.errors = initError();

            var user = new Model('users');
            user.addAttribute('password', this.form.password);
            user.addAttribute('email', this.form.email);

            this.$store.dispatch('installModule/install', user.serialize())
            .then(() => {
                window.location.replace('/');
            }).catch((err) => {
                console.log(err);
            });
        }
    }
};
</script>
