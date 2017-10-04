<template>
    <site>
        <div slot="content">
            <v-layout>
                <v-flex xs12 sm4 offset-sm4>
                    <v-card>
                        <v-card-title>
                            <h3 class="headline mb-0">Login</h3>
                        </v-card-title>
                        <v-card-text>
                            <v-text-field name="email"
                                label="Enter your email"
                                v-model="email"
                                :error-messages="emailErrors"
                                @input="$v.email.$touch()"
                                append-icon="mail"
                                type="email"
                                required
                                autofocus>
                            </v-text-field>
                            <v-text-field
                              name="password"
                              label="Enter your password"
                              v-model="password"
                              :error-messages="passwordErrors"
                              @input="$v.password.$touch()"
                              :append-icon="hidePassword ? 'visibility' : 'visibility_off'"
                              :append-icon-cb="() => (hidePassword = !hidePassword)"
                              :type="hidePassword ? 'password' : 'text'"
                              required
                            ></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn :disabled="$v.$invalid" flat @click="submit">Submit</v-btn>
                            <v-btn flat @click="clear">Clear</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </div>
    </site>
</template>

<script>
    import Model from '@/js/model';

    import { required, email } from 'vuelidate/lib/validators';

    import Site from '@/site/components/site.vue';

    var initError = function() {
      return {
        email : [],
        password : []
      }
    }

    export default {
        components : {
            Site
        },
        data() {
            return {
                email : '',
                password : '',
                hidePassword : true,
                errors : initError()
            }
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
                ! this.$v.email.required && errors.push('Email is required');
                ! this.$v.email.email && errors.push('Email is not valid');
                return errors;
            },
            passwordErrors() {
                const errors = [];
                if (! this.$v.password.$dirty) return errors;
                ! this.$v.password.required && errors.push('Password is required');
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
                    else if ( nv.response.status == 404 ){
                      //this.error = err.response.statusText;
                    }
                    else {
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
            },
            submit() {
                this.errors = initError();

                var user = new Model('users');
                user.addAttribute('email', this.email);
                user.addAttribute('password', this.password);

                this.$store.dispatch('login', user.serialize())
                    .then(() => {
                        console.log('success');
                    }).catch({
                    });
            }
        }
    };
</script>
