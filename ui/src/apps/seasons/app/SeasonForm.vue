<template>
    <Page>
        <template slot="title">
            {{ $t('seasons') }} &bull;&nbsp;
            <span v-if="creating">{{ $t('create') }}</span>
            <span v-else>{{ $t('update') }}</span>
        </template>
        <template slot="content">
            <div class="uk-container">
                <div uk-grid>
                    <div class="uk-width-1-1">
                        <form class="uk-form-stacked">
                            <uikit-input-text v-model="form.season.name" :validator="$v.form.season.name" :errors="nameErrors" id="name" :placeholder="$t('name_placeholder')">
                                {{ $t('name') }}:
                            </uikit-input-text>
                            <uikit-input-text v-model="form.season.start_date" :validator="$v.form.season.start_date" :errors="startDateErrors" id="start_date" :placeholder="$t('start_date_placeholder')">
                                {{ $t('start_date') }}:
                            </uikit-input-text>
                            <uikit-input-text v-model="form.season.end_date" :validator="$v.form.season.end_date" :errors="endDateErrors" id="end_date" :placeholder="$t('end_date_placeholder')">
                                {{ $t('end_date') }}:
                            </uikit-input-text>
                            <uikit-textarea v-model="form.season.remark" :validator="$v.form.season.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('remark_placeholder')">
                                {{ $t('remark') }}:
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
        </template>
    </Page>
</template>

<script>
    import moment from 'moment';
    import Season from '../models/Season';
    import seasonStore from '../store';

    import { validationMixin } from 'vuelidate';
    import { required } from 'vuelidate/lib/validators';
    import isDate from '@/js/isDate';

    import 'vue-awesome/icons/save';

    import Page from './Page.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitTextarea from '@/components/uikit/Textarea.vue';

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

    import messages from '../lang';

    export default {
        i18n : messages,
        components : {
            Page,
            UikitInputText,
            UikitTextarea
        },
        mixins: [
            validationMixin
        ],
        data() {
            return {
                season : new Season(),
                form : initForm(),
                errors : initError()
            }
        },
        computed : {
            creating() {
                return this.season != null && this.season.id == null;
            },
            error() {
                return this.$store.getters['seasonModule/error'];
            },
            nameErrors() {
                const errors = [...this.errors.name];
                if (! this.$v.form.season.name.$dirty) return errors;
                ! this.$v.form.season.name.required && errors.push(this.$t('required'));
                return errors;
            },
            startDateErrors() {
                var errors = [...this.errors.start_date];
                if (! this.$v.form.season.start_date.$dirty) return errors;
                if (!this.$v.form.season.start_date.isDate) errors.push(this.$t('invalid_date', { format : moment.localeData().longDateFormat('L') }));
                return errors;
            },
            endDateErrors() {
                var errors = [...this.errors.end_date];
                if (! this.$v.form.season.end_date.$dirty) return errors;
                if (!this.$v.form.season.end_date.isDate) errors.push(this.$t('invalid_date', { format : moment.localeData().longDateFormat('L') }));
                ! this.$v.form.season.end_date.after && errors.push(this.$t('invalid_end_date'));
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
                        isDate
                    },
                    end_date : {
                        required,
                        isDate,
                        after(value) {
                            if ( this.$v.form.season.end_date.isDate && this.$v.form.season.start_date.isDate) {
                                var startDate = moment(this.form.season.start_date, 'L');
                                var endDate = moment(value, 'L');
                                return startDate.isBefore(endDate);
                            }
                            return true;
                        }
                    },
                    remark : {
                    }
                }
            }
        },
        beforeCreate() {
            if (!this.$store.state.seasonModule) {
                this.$store.registerModule('seasonModule', seasonStore);
            }
        },
        created() {
            if (this.$route.params.id) {
                this.fetchData(this.$route.params.id);
            }
        },
        beforeRouteUpdate(to, from, next) {
        	if (to.params.id) this.fetchData(to.params.id);
        	next();
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
            async fetchData(id) {
                this.season = await this.$store.dispatch('seasonModule/read', {
                    id : id
                });
                this.fillForm();
            },
            clear() {
                this.$v.$reset();
                this.form = initForm();
            },
            fillForm() {
                this.form.season.name = this.season.name;
                this.form.season.start_date = this.season.formatted_start_date;
                this.form.season.end_date = this.season.formatted_end_date;
                this.form.season.remark = this.season.remark;
            },
            fillSeason() {
                this.season.name = this.form.season.name;
                this.season.start_date = moment(this.form.season.start_date, 'L');
                this.season.end_date = moment(this.form.season.end_date, 'L');
                this.season.description = this.form.season.description;
                this.season.remark = this.form.season.remark;
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
                this.fillSeason();
                this.$store.dispatch('seasonModule/save', this.season)
                    .then((newSeason) => {
                        this.$router.push({ name : 'seasons.read', params : { id : newSeason.id }});
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
        }
    };
</script>
