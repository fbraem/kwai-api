<template>
    <v-container fluid>
        <v-card class="mb-5">
            <v-card-title primary-title>
                <h4 class="headline mb-0">{{ $t('type_details') }}</h4>
            </v-card-title>
            <v-card-text>
                <v-container fluid grid-list-md>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field name="name"
                                :label="$t('type.form.name.label')"
                                :hint="$t('type.form.name.hint')"
                                v-model="form.teamtype.name"
                                :error-messages="nameErrors"
                                @input="$v.form.teamtype.name.$touch()"
                                required>
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 sm6>
                            <v-text-field name="start_age"
                                :label="$t('type.form.min_age.label')"
                                :hint="$t('type.form.min_age.hint')"
                                v-model="form.teamtype.start_age"
                                :error-messages="startAgeErrors"
                                @input="$v.form.teamtype.start_age.$touch()">
                            </v-text-field>
                        </v-flex>
                        <v-spacer></v-spacer>
                        <v-flex xs12 sm6>
                            <v-text-field name="end_age"
                                :label="$t('type.form.max_age.label')"
                                :hint="$t('type.form.max_age.hint')"
                                v-model="form.teamtype.end_age"
                                :error-messages="endAgeErrors"
                                @input="$v.form.teamtype.end_age.$touch()">
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-select
                                :items="genders"
                                v-model="form.teamtype.gender"
                                @input="$v.form.teamtype.gender.$touch"
                                :error-messages="genderErrors"
                                :label="$t('type.form.gender.label')"
                                :hint="$t('type.form.gender.hint')">
                            </v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field
                                name="remark"
                                :label="$t('type.form.remark.label')"
                                :hint="$t('type.form.remark.hint')"
                                v-model="form.teamtype.remark"
                                @input="$v.form.teamtype.remark.$touch"
                                :error-messages="remarkErrors"
                                textarea>
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs6>
                            <v-checkbox color="green" v-model="form.teamtype.active">
                                <div slot="label">{{ $t('active') }}</div>
                            </v-checkbox>
                        </v-flex>
                        <v-flex xs6>
                            <v-checkbox color="green" v-model="form.teamtype.competition">
                                <div slot="label">{{ $t('competition') }}</div>
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" :disabled="$v.$invalid" @click="submit">{{ $t('submit') }}</v-btn>
                <v-btn v-if="!teamtype" flat @click="clear">{{ $t('clear') }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
import moment from 'moment';
import Model from '@/js/model';

import { required, numeric } from 'vuelidate/lib/validators';

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
        teamtype : {
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

import messages from '../lang/lang';

export default {
    props : {
        teamtype : {
            type : Object
        }
    },
    i18n : {
        messages
    },
    data() {
        return {
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
        error() {
            return this.$store.state.teamModule.status.error;
        },
        nameErrors() {
            const errors = [...this.errors.name];
            if (! this.$v.form.teamtype.name.$dirty) return errors;
            ! this.$v.form.teamtype.name.required && errors.push('Name is required');
            return errors;
        },
        startAgeErrors() {
            const errors = [...this.errors.start_age];
            if (! this.$v.form.teamtype.start_age.$dirty) return errors;
            ! this.$v.form.teamtype.start_age.numeric && errors.push('Start age must be numeric');
            return errors;
        },
        endAgeErrors() {
            const errors = [...this.errors.end_age];
            if (! this.$v.form.teamtype.end_age.$dirty) return errors;
            ! this.$v.form.teamtype.start_age.numeric && errors.push('End age must be numeric');
            return errors;
        },
        genderErrors() {
            const errors = [...this.errors.gender];
            if (! this.$v.form.teamtype.gender.$dirty) return errors;
            //! this.$v.form.teamtype.gender.numeric && errors.push('End age must be numeric');
            return errors;
        },
        remarkErrors() {
            const errors = [...this.errors.remark];
            if (! this.$v.form.teamtype.remark.$dirty) return errors;
            return errors;
        }
    },
    validations : {
        form : {
            teamtype : {
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
    mounted() {
        if ( this.teamtype ) this.fillForm(this.teamtype);
    },
    watch : {
        teamtype(nv) {
            if (nv) {
                this.fillForm(nv);
            }
        },
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
            this.form = initForm();
        },
        fillForm(model) {
            this.form.teamtype.name = model.name;
            this.form.teamtype.start_age = model.start_age;
            this.form.teamtype.end_age = model.end_age;
            this.form.teamtype.gender = model.gender;
            this.form.teamtype.active = model.active;
            this.form.teamtype.competition = model.competition;
            this.form.teamtype.remark = model.remark;
        },
        fillModel(model) {
            model.name =this.form.teamtype.name;
            model.start_age = this.form.teamtype.start_age;
            model.end_age = this.form.teamtype.end_age;
            model.gender = this.form.teamtype.gender;
            model.active = this.form.teamtype.active;
            model.competition = this.form.teamtype.competition;
            model.remark =this.form.teamtype.remark;
        },
        submit() {
            this.errors = initError();

            if (this.teamtype) { // update
                this.fillModel(this.teamtype);
                this.$store.dispatch('teamModule/updateType', this.teamtype)
                    .then(() => {
                        this.$router.push({ name : 'teamtype.read', params : { id : this.teamtype.id }});
                    }).catch(() => {
                        console.log("Error occurred in teamModule/updateType");
                    });
            } else { // create
                var teamtype = new Model('team_types');
                this.fillModel(teamtype);
                this.$store.dispatch('teamModule/createType', teamtype)
                    .then((newType) => {
                        this.$router.push({ name : 'teamtype.read', params : { id : newType.id }});
                    }).catch(err => {
                    });
            }
        }
    }
};
</script>
