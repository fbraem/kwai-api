<template>
    <Page>
        <template slot="title">
            {{ $t('invitation.title') }}
        </template>
        <div v-if="invitation" slot="content" class="uk-container">
            <div v-if="invitation.isExpired" class="uk-alert-danger" uk-alert>
                <p>
                    {{ $t('invitation.expired') }}
                </p>
            </div>
            <div v-else>
                <div class="uk-width-1-1" uk-grid>
                    <div class="uk-width-1-1 uk-alert-primary" uk-alert>
                        {{ $t('invitation.intro') }}
                    </div>
                    <div class="uk-width-1-1">
                        <form class="uk-form-stacked">
                            <uikit-input-text v-model="form.user.first_name" :validator="$v.form.user.first_name" :errors="firstNameErrors" id="first_name" :placeholder="$t('form.first_name.placeholder')">
                                {{ $t('form.first_name.label') }}:
                            </uikit-input-text>
                            <uikit-input-text v-model="form.user.last_name" :validator="$v.form.user.last_name" :errors="lastNameErrors" id="last_name" :placeholder="$t('form.last_name.placeholder')">
                                {{ $t('form.last_name.label') }}:
                            </uikit-input-text>
                            <uikit-email v-model="form.user.email" :validator="$v.form.user.email" :errors="emailErrors" id="email" :placeholder="$t('form.email.placeholder')">
                                {{ $t('form.email.label') }}:
                            </uikit-email>
                            <uikit-password v-model="form.user.password" :validator="$v.form.user.password" :errors="passwordErrors" id="password" :placeholder="$t('form.password.placeholder')">
                                {{ $t('form.password.label') }}:
                            </uikit-password>
                            <uikit-password v-model="form.user.retype_password" :validator="$v.form.user.retype_password" :errors="retypePasswordErrors" id="retype_password" :placeholder="$t('form.retype_password.placeholder')">
                                {{ $t('form.retype_password.label') }}:
                            </uikit-password>
                        </form>
                    </div>
                    <div uk-grid class="uk-width-1-1">
                        <div class="uk-width-expand">
                        </div>
                        <div class="uk-width-auto">
                            <button class="uk-button uk-button-primary" :disabled="$v.$invalid" @click="submit">
                                <fa-icon name="save" />&nbsp; {{ $t('save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else slot="content">
            <div v-if="error && error.response.status == 404" class="uk-alert-danger" uk-alert>
                {{ $t('invitation.not_found') }}
            </div>
        </div>
    </Page>
</template>

<script>
    import 'vue-awesome/icons/save';

    import Page from './Page.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitPassword from '@/components/uikit/Password.vue';
    import UikitEmail from '@/components/uikit/Email.vue';

    import { validationMixin } from 'vuelidate';
    import { required, email, sameAs, minLength } from 'vuelidate/lib/validators';
    import passwordComplexity from '@/js/passwordComplexity'

    import messages from './lang';

    import User from '@/models/User';
    import userStore from '@/stores/users';

    var initError = function() {
        return {
            first_name : [],
            last_name : [],
            email : [],
            password : [],
            retype_password : []
        }
    };
    var initForm = function() {
        return {
            user : {
                first_name : '',
                last_name : '',
                email : '',
                password : '',
                retype_password : ''
            }
        };
    }

    export default {
        i18n : messages,
        components : {
            Page,
            UikitInputText,
            UikitPassword,
            UikitEmail
        },
        data() {
            return {
                invitation : null,
                form : initForm(),
                errors : initError()
            };
        },
        computed : {
            error() {
                return this.$store.getters['userModule/error'];
            },
            firstNameErrors() {
                const errors = [...this.errors.first_name];
                if (! this.$v.form.user.first_name.$dirty) return errors;
                ! this.$v.form.user.first_name.required && errors.push(this.$t('required'));
                return errors;
            },
            lastNameErrors() {
                const errors = [...this.errors.last_name];
                if (! this.$v.form.user.last_name.$dirty) return errors;
                ! this.$v.form.user.last_name.required && errors.push(this.$t('required'));
                return errors;
            },
            emailErrors() {
                const errors = [...this.errors.email];
                if (! this.$v.form.user.email.$dirty) return errors;
                ! this.$v.form.user.email.required && errors.push(this.$t('required'));
                ! this.$v.form.user.email.email && errors.push(this.$t('form.email.invalid'));
                return errors;
            },
            passwordErrors() {
                const errors = [...this.errors.password];
                if (! this.$v.form.user.password.$dirty) return errors;
                ! this.$v.form.user.password.required && errors.push(this.$t('required'));
                ! this.$v.form.user.password.passwordComplexity && errors.push(this.$t('form.password.complex'));
                ! this.$v.form.user.password.minLength && errors.push(this.$t('form.password.minLength'));
                return errors;
            },
            retypePasswordErrors() {
                const errors = [...this.errors.retype_password];
                if (! this.$v.form.user.retype_password.$dirty) return errors;
                ! this.$v.form.user.retype_password.required && errors.push(this.$t('required'));
                ! this.$v.form.user.retype_password.sameAs && errors.push(this.$t('form.retype_password.sameas'));
                return errors;
            }
        },
        mixins: [
            validationMixin
        ],
        validations : {
            form : {
                user : {
                    email : {
                        required,
                        email
                    },
                    first_name : {
                        required
                    },
                    last_name : {
                        required
                    },
                    password : {
                        required,
                        minLength : minLength(8),
                        passwordComplexity
                    },
                    retype_password : {
                        required,
                        sameAs : sameAs('password')
                    }
                }
            }
        },
        created() {
            if (!this.$store.state.userModule) {
                this.$store.registerModule('userModule', userStore);
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchData();
            next();
        },
        mounted() {
            this.fetchData();
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
            fetchData() {
                this.$store.dispatch('userModule/readInvitationByToken', { token : this.$route.params.token })
                    .then((invitation) => {
                        this.invitation = invitation;
                    })
                    .catch((error) => {
                        this.error = error;
                    });
            },
            submit() {
                var user = new User();
                user.first_name = this.form.user.first_name;
                user.last_name = this.form.user.last_name;
                user.email = this.form.user.email;
                user.password = this.form.user.password;
                this.$store.dispatch('userModule/createWithToken', {
                        user : user,
                        token : this.$route.params.token
                    })
                    .then((user) => {
                        this.$router.push({ name : 'home' });
                    });
            }
        }
    };
</script>
