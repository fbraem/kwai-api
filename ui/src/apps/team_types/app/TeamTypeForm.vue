<template>
    <Page>
        <template slot="title">
            {{ $t('types') }} &bull;&nbsp;
            <span v-if="creating">{{ $t('create') }}</span>
            <span v-else>{{ $t('update') }}</span>
        </template>
        <div slot="content" class="uk-container">
            <div uk-grid>
                <div class="uk-width-1-1">
                    <form class="uk-form-stacked">
                        <uikit-input-text v-model="form.team_type.name" :validator="$v.form.team_type.name" :errors="nameErrors" id="name" :placeholder="$t('form.name.placeholder')">
                            {{ $t('form.name.label') }}:
                        </uikit-input-text>
                        <uikit-input-text v-model="form.team_type.start_age" :validator="$v.form.team_type.start_age" :errors="startAgeErrors" id="start_age" :placeholder="$t('form.start_age.placeholder')">
                            {{ $t('form.start_age.label') }}:
                        </uikit-input-text>
                        <uikit-input-text v-model="form.team_type.end_age" :validator="$v.form.team_type.end_age" :errors="endAgeErrors" id="end_age" :placeholder="$t('form.end_age.placeholder')">
                            {{ $t('form.end_age.label') }}:
                        </uikit-input-text>
                        <uikit-select v-model="form.team_type.gender" :items="genders" :validator="$v.form.team_type.gender" :errors="genderErrors" id="gender">
                            {{ $t('form.gender.label') }}:
                        </uikit-select>
                        <uikit-checkbox v-model="form.team_type.active">
                            {{ $t('active') }}
                        </uikit-checkbox>
                        <uikit-checkbox v-model="form.team_type.competition">
                            {{ $t('competition') }}
                        </uikit-checkbox>
                        <uikit-textarea v-model="form.team_type.remark" :validator="$v.form.team_type.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('form.remark.placeholder')">
                            {{ $t('form.remark.label') }}:
                        </uikit-textarea>
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

    import teamTypeStore from '@/stores/team_types';
    import TeamType from '@/models/TeamType';

    import { validationMixin } from 'vuelidate';
    import { required, numeric } from 'vuelidate/lib/validators';

    import Page from './Page.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitTextarea from '@/components/uikit/Textarea.vue';
    import UikitSelect from '@/components/uikit/Select.vue';
    import UikitCheckbox from '@/components/uikit/Checkbox.vue';

    var initError = function() {
        return {
            name : [],
            start_age : [],
            end_age : [],
            gender : [],
            active : [],
            competition : [],
            remark : [],
        }
    };
    var initForm = function() {
        return {
            team_type : {
                name : '',
                start_age : '',
                end_age : '',
                gender : 0,
                active : true,
                competition : true,
                remark : ''
            }
        };
    }

    import messages from '../lang';

    export default {
        components : {
            Page, UikitInputText, UikitTextarea, UikitSelect, UikitCheckbox
        },
        i18n : messages,
        mixins: [
            validationMixin
        ],
        data() {
            return {
                teamType : new TeamType(),
                form : initForm(),
                errors : initError(),
                genders : [
                    { value : 0, text : this.$t('no_restriction') },
                    { value : 1, text : this.$t('male') },
                    { value : 2, text : this.$t('female') }
                ]
            }
        },
        computed : {
            creating() {
                return this.teamType != null && this.teamType.id == null;
            },
            error() {
                return this.$store.getters['teamTypeModule/error'];
            },
            nameErrors() {
                const errors = [...this.errors.name];
                if (! this.$v.form.team_type.name.$dirty) return errors;
                ! this.$v.form.team_type.name.required && errors.push('Name is required');
                return errors;
            },
            startAgeErrors() {
                const errors = [...this.errors.start_age];
                if (! this.$v.form.team_type.start_age.$dirty) return errors;
                ! this.$v.form.team_type.start_age.numeric && errors.push(this.$t('numeric'));
                return errors;
            },
            endAgeErrors() {
                const errors = [...this.errors.end_age];
                if (! this.$v.form.team_type.end_age.$dirty) return errors;
                ! this.$v.form.team_type.end_age.numeric && errors.push(this.$t('numeric'));
                return errors;
            },
            genderErrors() {
                const errors = [...this.errors.gender];
                if (! this.$v.form.team_type.gender.$dirty) return errors;
                //! this.$v.form.teamtype.gender.numeric && errors.push('End age must be numeric');
                return errors;
            },
            remarkErrors() {
                const errors = [...this.errors.remark];
                if (! this.$v.form.team_type.remark.$dirty) return errors;
                return errors;
            }
        },
        validations : {
            form : {
                team_type : {
                    name : {
                        required
                    },
                    start_age : {
                        numeric
                    },
                    end_age : {
                        numeric
                    },
                    gender : {
                    },
                    remark : {
                    }
                }
            }
        },
        beforeCreate() {
            if (!this.$store.state.teamTypeModule) {
                this.$store.registerModule('teamTypeModule', teamTypeStore);
            }
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                if (to.params.id) await vm.fetchData(to.params.id);
                next();
            });
        },
        watch : {
            errors(nv) {
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
                this.form = initForm();
            },
            async fetchData(id) {
                this.teamType = await this.$store.dispatch('teamTypeModule/read', {
                    id : id
                });
                this.fillForm();
            },
            fillForm() {
                this.form.team_type.name = this.teamType.name;
                this.form.team_type.start_age = this.teamType.start_age;
                this.form.team_type.end_age = this.teamType.end_age;
                this.form.team_type.gender = this.teamType.gender;
                this.form.team_type.active = this.teamType.active;
                this.form.team_type.competition = this.teamType.competition;
                this.form.team_type.remark = this.teamType.remark;
            },
            fillTeamType() {
                this.teamType.name =this.form.team_type.name;
                this.teamType.start_age = this.form.team_type.start_age;
                this.teamType.end_age = this.form.team_type.end_age;
                this.teamType.gender = this.form.team_type.gender;
                this.teamType.active = this.form.team_type.active;
                this.teamType.competition = this.form.team_type.competition;
                this.teamType.remark =this.form.team_type.remark;
            },
            submit() {
                this.errors = initError();
                this.fillTeamType();
                this.$store.dispatch('teamTypeModule/save', this.teamType)
                    .then((newType) => {
                        this.$router.push({ name : 'team_types.read', params : { id : newType.id }});
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
        }
    };
</script>
