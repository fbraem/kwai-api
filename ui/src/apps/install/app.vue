<template>
    <site>
        <div slot="content">
            <section class="uk-section uk-container uk-container-expand">
                <div class="uk-flex uk-flex-center">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-header">
                            <h3 class="uk-card-title"><i uk-icon="icon: user"></i> Install Clubman</h3>
                            <p class="uk-text-meta uk-text-small uk-margin-remove-top">Create the root user for Clubman</p>
                        </div>
                        <div class="uk-card-body">
                            <form class="uk-form-horizontal">
                                <div class="uk-margin">
                                    <label class="uk-form-label" :class="{ 'uk-text-danger': $v.email.$error }" for="email">Email:</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline">
                                            <span class="uk-form-icon" uk-icon="icon: mail"></span>
                                            <input class="uk-input" v-model="email" id="email" type="email"
                                                placeholder="Email" required="required" autofocus="autofocus"
                                                :class="{ 'uk-form-danger' : $v.email.$error, 'uk-form-success' : $v.email.$dirty && !$v.email.$error }"
                                                @input="$v.email.$touch()"
                                                />
                                        </div>
                                        <div v-if="$v.email.$dirty && !$v.email.required" class="uk-text-danger uk-text-small">Email is required.</div>
                                        <div v-if="$v.email.$dirty && !$v.email.email" class="uk-text-danger uk-text-small">Email is not valid.</div>
                                    </div>
                                </div>
                                <div class="uk-margin">
                                    <label class="uk-form-label" :class="{ 'uk-text-danger': $v.password.$error }" for="pwd">Password:</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline">
                                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                                            <input class="uk-input" v-model="password" id="pwd" type="password" placeholder="Password" required="required"
                                            :class="{ 'uk-form-danger' : $v.password.$error, 'uk-form-success' : $v.password.$dirty && !$v.password.$error }"
                                            @input="$v.password.$touch()"
                                            />
                                        </div>
                                        <div v-if="$v.password.$dirty && !$v.password.required" class="uk-text-danger uk-text-small">Password is required.</div>
                                        <div v-if="$v.password.$dirty && !$v.password.minLength" class="uk-text-danger uk-text-small">Password must contain at least {{ $v.password.$params.minLength.min }} characters.</div>
                                    </div>
                                </div>
                                <div class="uk-margin">
                                    <label class="uk-form-label" :class="{ 'uk-text-danger': $v.retype.$error }" for="rpwd">Retype Password:</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline">
                                            <span class="uk-form-icon" uk-icon="icon: lock"></span>
                                            <input class="uk-input" v-model="retype" id="rpwd" type="password" placeholder="Retype Password" required="required"
                                                :class="{ 'uk-form-danger' : $v.retype.$error, 'uk-form-success' : $v.retype.$dirty && !$v.retype.$error }"
                                                @input="$v.retype.$touch()"
                                            />
                                        </div>
                                        <div v-if="$v.retype.$dirty && !$v.retype.sameAsPassword" class="uk-text-danger uk-text-small">Passwords are not identical.</div>
                                    </div>
                                </div>
                            </form>
                            <div v-if="error" class="uk-alert-danger" uk-alert>
                                {{ this.error }}
                            </div>
                        </div>
                        <div class="uk-card-footer">
                            <button :disabled="$v.$invalid" class="uk-button uk-button-primary" v-on:click="click">Install</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </site>
</template>

<script>
import Model from '@/js/model';

import Site from '@/site/components/site.vue';

import { required, minLength, email, sameAs } from 'vuelidate/lib/validators';

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
            retype : '',
            errors : initError(),
            error : ''
        };
    },
    validations : {
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
    },
    mounted() {
    },
    methods : {
        click() {
            this.errors = initError();
            this.error = false;

            var user = new Model('users');
            user.addAttribute('password', this.password);
            user.addAttribute('email', this.email);

            this.$store.dispatch('installModule/install', user.serialize())
            .then(() => {
                console.log('success');
            }).catch(err => {
                if ( err.response.status == 400 ) {
                    err.response.data.errors.forEach((item, index) => {
                        if (item.source.pointer == '/data/attributes/email') {
                            this.errors.email.push(item.title);
                        } else if (item.source.pointer == '/data/attributes/password') {
                            this.errors.password.push(item.title);
                        }
                    });
                }
                else if ( err.response.status == 404 ) {
                    this.error = err.response.statusText;
                }
                else if ( err.response.status == 403 ) {
                    this.error = err.response.statusText;
                } else {
                    //TODO: check if we can get here ...
                    console.log(err.response);
                }
            });
        }
    }
};
</script>
