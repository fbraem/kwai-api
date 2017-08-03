<template>
    <site>
        <div slot="content">
            <section class="uk-section uk-container uk-container-expand">
                <div class="uk-flex uk-flex-center">
                    <div class="uk-card uk-card-default">
                        <div class="uk-card-header">
                            <h3 class="uk-card-title"><i uk-icon="icon: user"></i> Clubman</h3>
                            <p class="uk-text-meta uk-text-small uk-margin-remove-top">Login to Clubman</p>
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
                                    </div>
                                </div>
                            </form>
                            <div v-if="error" class="uk-alert-danger" uk-alert>
                                {{ this.error }}
                            </div>
                        </div>
                        <div class="uk-card-footer">
                            <button :disabled="$v.$invalid" class="uk-button uk-button-primary" v-on:click="click">Login</button>
                        </div>
                    </div>
                </div>
            </section>
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
                errors : initError(),
                error : false
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
        mounted() {
        },
        methods : {
            click() {
                this.errors = initError();
                this.error = false;

                var user = new Model('users');
                user.addAttribute('email', this.email);
                user.addAttribute('password', this.password);

                this.$store.dispatch('login', user.serialize())
                    .then(() => {
                        console.log('success');
                    }).catch(err => {
                      if ( err.response.status == 400 ) {
                        err.response.data.forEach((item, index) => {
                          if (item.source.pointer == '/data/attributes/email') {
                            this.errors.email.push(item.title);
                          } else if (item.source.pointer == '/data/attributes/password') {
                            this.errors.password.push(item.title);
                          }
                        });
                      }
                      else if ( err.response.status == 404 ){
                        this.error = err.response.statusText;
                      }
                      else {
                        //TODO: check if we can get here ...
                        console.log(err);
                      }
                    });
            }
        }
    };
</script>
