<template>
    <v-container fluid>
        <v-card class="mb-5">
            <v-card-title primary-title>
                <h4 class="headline mb-0">Season Details</h4>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-text-field name="name"
                            label="Name of the season"
                            v-model="form.season.name"
                            :error-messages="nameErrors"
                            @input="$v.form.season.name.$touch()"
                            required
                            >
                        </v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <date-field
                            name="start_date"
                            label="Start Date"
                            v-model="form.season.start_date"
                            :errors="startDateErrors"
                            :$v="$v.form.season.start_date"
                            :allowedDates="allowedStartDates"
                        />
                    </v-flex>
                    <v-flex xs12>
                        <date-field
                            name="end_date"
                            label="End Date"
                            v-model="form.season.end_date"
                            :errors="endDateErrors"
                            :$v="$v.form.season.end_date"
                            :allowedDates="allowedEndDates"
                        />
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field
                            name="remark"
                            v-model="form.season.remark"
                            @input="$v.form.season.remark.$touch"
                            :error-messages="remarkErrors"
                            label="Remark"
                            textarea>
                        </v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" :disabled="$v.$invalid" @click="submit">Submit</v-btn>
                <v-btn flat @click="clear">Clear</v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
import moment from 'moment';
import Model from '@/js/model';

import { required } from 'vuelidate/lib/validators';
import { withParams } from 'vuelidate/lib';
const dateValidator = withParams({ type: 'date' }, (value) => {
    if (value != null) {
        var m = moment(value, "L", true);
        return m.isValid();
    }
    return true;
});

import DateField from '@/components/DateField.vue';

var initError = function() {
    return {
        name : [],
        start_date : [],
        end_date : [],
        remark : []
    }
};
var initForm = function() {
    return {
        season : {
            name : '',
            start_date : moment().format('L'),
            end_date : '',
            remark : ''
        }
    };
}

import messages from '../lang/lang';

export default {
    components : {
        DateField
    },
    props : {
        season : {
            type : Object
        }
    },
    i18n : {
        messages
    },
    data() {
        return {
            form : initForm(),
            errors : initError()
        }
    },
    computed : {
        error() {
            return this.$store.state.seasonModule.status.error;
        },
        nameErrors() {
            const errors = [...this.errors.name];
            if (! this.$v.form.season.name.$dirty) return errors;
            ! this.$v.form.season.name.required && errors.push('Name is required');
            return errors;
        },
        startDateErrors() {
            var errors = [...this.errors.start_date];
            if (! this.$v.form.season.start_date.$dirty) return errors;
            if (!this.$v.form.season.start_date.dateValidator) errors.push('Start date is not a valid date. Format must be ' + moment.localeData().longDateFormat('L'));
            return errors;
        },
        endDateErrors() {
            var errors = [...this.errors.end_date];
            if (! this.$v.form.season.end_date.$dirty) return errors;
            if (!this.$v.form.season.end_date.dateValidator) errors.push('End date is not a valid date. Format must be ' + moment.localeData().longDateFormat('L'));
            return errors;
        },
        remarkErrors() {
            const errors = [...this.errors.remark];
            if (! this.$v.form.season.remark.$dirty) return errors;
            return errors;
        }
    },
    validations : {
        form : {
            season : {
                name : {
                    required
                },
                start_date : {
                    required,
                    dateValidator
                },
                end_date : {
                    required,
                    dateValidator
                },
                remark : {
                }
            }
        }
    },
    mounted() {
        if ( this.season ) this.fillForm(this.season);
    },
    watch : {
        season(nv) {
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
            this.form.season.name = model.name;
            if (model.start_date) {
                var date = moment(model.start_date, 'YYYY-MM-DD');
                this.form.season.start_date = date.format('L');
            }
            if (model.end_date) {
                var date = moment(model.end_date, 'YYYY-MM-DD');
                this.form.season.end_date = date.format('L');
            }
            this.form.season.remark = model.remark;
        },
        fillModel(model) {
            model.addAttribute('name', this.form.season.name);
            model.addAttribute('start_date', moment(this.form.season.start_date, 'L').format('YYYY-MM-DD'));
            model.addAttribute('end_date', moment(this.form.season.end_date, 'L').format('YYYY-MM-DD'));
            model.addAttribute('description', this.form.season.description);
            model.addAttribute('remark', this.form.season.remark);
        },
        allowedEndDates(date) {
            if (this.form.season.start_date && this.form.season.start_date.length > 0) {
                return moment(this.form.season.start_date, 'L').isSameOrBefore(date);
            }
            return true;
        },
        allowedStartDates(date) {
            if (this.form.season.end_date && this.form.season.end_date.length > 0) {
                return moment(this.form.season.end_date, 'L').isSameOrAfter(date);
            }
            return true;
        },
        submit() {
            this.errors = initError();

            if (this.season) { // update
                this.fillModel(this.season);
                this.$store.dispatch('seasonModule/update', this.season.serialize())
                    .then(() => {
                        this.$router.push({ name : 'season.read', params : { id : this.season.id }});
                    }).catch(() => {
                        console.log("Error occurred in seasonModule/update");
                    });
            } else { // create
                var season = new Model('seasons');
                this.fillModel(season);
                this.$store.dispatch('seasonModule/create', season.serialize())
                    .then((newSeason) => {
                        this.$router.push({ name : 'season.read', params : { id : newSeason.id }});
                    }).catch(err => {
                    });
            }
        }
    }
};

</script>
