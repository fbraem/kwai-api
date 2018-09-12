<template>
    <Page>
        <template slot="title">
            {{ $t('invite') }}
        </template>
        <div slot="content" class="uk-container">
            <div class="uk-width-1-1" uk-grid>
                <div class="uk-width-1-1 uk-alert-primary" uk-alert>
                    {{ $t('invite_intro') }}
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
    </Page>
</template>

<script>
    import 'vue-awesome/icons/save';

    import Page from './Page.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitEmail from '@/components/uikit/Email.vue';

    import { validationMixin } from 'vuelidate';
    import { required, email } from 'vuelidate/lib/validators';

    import messages from '../lang';

    import UserInvitation from '../models/UserInvitation';
    import userStore from '@/apps/users/store';

    var initError = function() {
        return {
            first_name : [],
            last_name : [],
            email : []
        }
    };
    var initForm = function() {
        return {
            user : {
                first_name : '',
                last_name : '',
                email : ''
            }
        };
    }

    export default {
        i18n : messages,
        components : {
            Page,
            UikitInputText,
            UikitEmail
        },
        data() {
            if (!this.$store.state.userModule) {
                this.$store.registerModule('userModule', userStore);
            }
            return {
                form : initForm(),
                errors : initError()
            };
        },
        computed : {
            error() {
                //console.log(this.$store.state.userModule.status.error);
                //return this.$store.state.userModule.status.error;
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
                    }
                }
            }
        },
        watch : {
            error(nv) {
                if (nv) {
                    console.log(nv);
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
            submit() {
                this.errors = initError();
                var invitation = new UserInvitation();
                invitation.first_name = this.form.user.first_name;
                invitation.last_name = this.form.user.last_name;
                invitation.email = this.form.user.email;
                this.$store.dispatch('userModule/invite', invitation)
                    .then((invitation) => {
                        this.$router.push({ name : 'home' });
                    })
                    .catch((error) => {
                        console.log(this.$config);
                        console.log(error);
                    });
            }
        }
    };
</script>
