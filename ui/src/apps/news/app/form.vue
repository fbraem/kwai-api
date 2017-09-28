<template>
    <v-container fluid>
        <v-stepper v-model="stepper" non-linear>
            <v-stepper-header>
                <v-stepper-step step="1" :complete="!$v.step1Group.$invalid" editable>Enter the story</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step step="2" :complete="!$v.step2Group.$invalid" :editable="!$v.step1Group.$invalid">Publish Information</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step step="3" :complete="!$v.step3Group.$invalid" :editable="!$v.step1Group.$invalid && !$v.step2Group.$invalid">Featured Information</v-stepper-step>
            </v-stepper-header>
            <v-stepper-content step="1">
                <v-card class="mb-5">
                    <v-card-title primary-title>
                        <h4 class="headline mb-0">Story Details</h4>
                    </v-card-title>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field name="title"
                                    label="Title of the story"
                                    v-model="form.story.title"
                                    :error-messages="titleErrors"
                                    @input="$v.form.story.title.$touch()"
                                    required
                                    >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-select
                                    :items="categories"
                                    v-model="form.story.category"
                                    @input="$v.form.story.category.$touch"
                                    :error-messages="categoryErrors"
                                    label="Category"
                                    required>
                                </v-select>
                            </v-flex>
                            <v-flex xs12>
                                <v-layout row wrap>
                                    <v-flex xs6>
                                        <v-text-field
                                            name="summary"
                                            v-model="form.story.summary"
                                            @input="$v.form.story.summary.$touch"
                                            :error-messages="summaryErrors"
                                            label="Summary"
                                            textarea
                                            required
                                            hint="Use Markdown for styling">
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs6>
                                        Preview
                                        <div v-html="summaryHtml"></div>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12>
                                <v-layout row wrap>
                                    <v-flex xs6>
                                        <v-text-field
                                            name="content"
                                            v-model="form.story.content"
                                            @input="$v.form.story.content.$touch"
                                            :error-messages="contentErrors"
                                            label="Content"
                                            textarea
                                            hint="Use Markdown for styling">
                                        </v-text-field>
                                    </v-flex>
                                    <v-flex xs6>
                                        Preview
                                        <div v-html="contentHtml"></div>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field
                                    name="remark"
                                    v-model="form.story.remark"
                                    @input="$v.form.story.remark.$touch"
                                    :error-messages="remarkErrors"
                                    label="Remark"
                                    textarea
                                    hint="Enter a remark about this news story">
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
                <v-btn flat :disabled="$v.step1Group.$invalid" @click.native="stepper=2">Next</v-btn>
            </v-stepper-content>
            <v-stepper-content step="2">
                <v-card class="mb-5">
                    <v-card-title primary-title>
                        <h4 class="headline mb-0">Publish Information</h4>
                    </v-card-title>
                    <v-card-text>
                        <v-flex xs12>
                            <v-switch :label="form.story.enabled ? 'Enabled' : 'Disabled'" v-model="form.story.enabled">
                            </v-switch>
                        </v-flex>
                        <v-flex xs12>
                            <date-field
                                name="publish_date"
                                label="Pubish Date"
                                v-model="form.story.publish_date"
                                :errors="publishDateErrors"
                                :$v="$v.form.story.publish_date">
                            </date-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field name="publish_time"
                                label="Publish Time"
                                v-model="form.story.publish_time"
                                :error-messages="publishTimeErrors"
                                hint="Format HH:MM"
                                @input="$v.form.story.publish_time.$touch()"
                                append-icon="access_time">
                            </v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <date-field
                                name="end_date"
                                label="End Date"
                                v-model="form.story.end_date"
                                :$v="$v.form.story.end_date"
                                :errors="endDateErrors"
                                :allowed-dates="allowedDates"
                                >
                            </date-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field name="end_time"
                                label="End Time"
                                v-model="form.story.end_time"
                                :error-messages="endTimeErrors"
                                hint="Format HH:MM"
                                @input="$v.form.story.end_time.$touch()"
                                append-icon="access_time">
                            </v-text-field>
                        </v-flex>
                    </v-card-text>
                </v-card>
                <v-btn flat :disable="$v.step2Group.$invalid" @click.native="stepper=3">Next</v-btn>
                <v-btn flat :disable="$v.step2Group.$invalid" @click.native="stepper=1">Previous</v-btn>
            </v-stepper-content>
            <v-stepper-content step="3">
                <v-card class="mb-5">
                    <v-card-title primary-title>
                        <div>
                            <h4 class="headline mb-0">Featured News</h4>
                            <div>
                                A featured news story will be shown on top or can be selected to show on the front page.
                            </div>
                        </div>
                    </v-card-title>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field
                                    name="featured"
                                    v-model="form.story.featured"
                                    @input="$v.form.story.featured.$touch"
                                    :error-messages="featuredErrors"
                                    label="Priority"
                                    hint="A story with priority 0 means that it will not be listed in the featured news stories."
                                    required>
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <date-field
                                    name="featured_end_date"
                                    label="Featured End Date"
                                    v-model="form.story.featured_end_date"
                                    :$v="$v.form.story.featured_end_date"
                                    :errors="featuredEndDateErrors"
                                    :allowed-dates="allowedDates"
                                    >
                                </date-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field name="featured_end_time"
                                    label="Featured End Time"
                                    v-model="form.story.featured_end_time"
                                    :error-messages="featuredEndTimeErrors"
                                    hint="Format HH:MM"
                                    @input="$v.form.story.feature_end_time.$touch()"
                                    append-icon="access_time">
                                </v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
                <v-btn flat @click.native="stepper=2">Previous</v-btn>
            </v-stepper-content>
        </v-stepper>
        <v-btn primary :disabled="$v.$invalid" @click="submit">Submit</v-btn>
        <v-btn flat @click="clear">Clear</v-btn>
    </v-container>
</template>

<script>
    import Model from '@/js/model';
    import moment from 'moment';
    import marked from 'marked';

    import { required, numeric } from 'vuelidate/lib/validators';
    import { withParams } from 'vuelidate/lib';

    const timeValidator = withParams({ type : 'time' }, (value) => {
        if ( value != null ) return /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/.test(value);
        return true;
    });
    const dateValidator = withParams({ type: 'date' }, (value) => {
        if (value != null) {
            var m = moment(value, "L", true);
            return m.isValid();
        }
        return true;
    });

    import DateField from './date.vue';

    var initError = function() {
        return {
            title : [],
            category : [],
            summary : [],
            content : [],
            enabled : [],
            publish_date : [],
            end_date : [],
            remark : [],
            featured : [],
            featured_end_date : []
        }
    };

    export default {
        props : {
            story : {
                type : Object
            }
        },
        components : {
            DateField
        },
        data() {
            return {
                form : {
                    story : {
                        title : '',
                        category : 0,
                        summary : '',
                        content : '',
                        enabled : true,
                        publish_date : moment().format('L'),
                        publish_time : moment().format('HH:mm'),
                        end_date : null,
                        end_time : null,
                        featured : 0,
                        featured_end_date : null,
                        featured_end_time : null,
                        remark : ''
                    }
                },
                errors : initError(),
                error : false,
                stepper : 1
            }
        },
        computed : {
            categories() {
                return this.$store.state.newsModule.categories.map((category) => ({value : category.id, text : category.name }));
            },
            dateFormat() {
                return 'Format ' + moment.localeData().longDateFormat('L');
            },
            titleErrors() {
                const errors = [...this.errors.title];
                if (! this.$v.form.story.title.$dirty) return errors;
                ! this.$v.form.story.title.required && errors.push('Title is required');
                return errors;
            },
            categoryErrors() {
                const errors = [...this.errors.category];
                if (! this.$v.form.story.category.$dirty) return errors;
                ! this.$v.form.story.category.required && errors.push('Category is required');
                return errors;
            },
            summaryErrors() {
                const errors = [...this.errors.summary];
                if (! this.$v.form.story.summary.$dirty) return errors;
                ! this.$v.form.story.summary.required && errors.push('Category is required');
                return errors;
            },
            contentErrors() {
                const errors = this.errors.content;
                return errors;
            },
            remarkErrors() {
                const errors = this.errors.remark;
                return errors;
            },
            publishDateErrors() {
                var errors = [...this.errors.publish_date];
                if (! this.$v.form.story.publish_date.$dirty) return errors;
                if (!this.$v.form.story.publish_date.dateValidator) errors.push('Publish date is not a valid date. Format must be ' + moment.localeData().longDateFormat('L'));
                return errors;
            },
            publishTimeErrors() {
                const errors = [];
                if (! this.$v.form.story.publish_time.$dirty) return errors;
                ! this.$v.form.story.publish_time.timeValidator && errors.push('Time is not valid. Format must be HH:MM');
                return errors;
            },
            endDateErrors() {
                const errors = [...this.errors.end_date];
                if (! this.$v.form.story.end_date.$dirty) return errors;
                ! this.$v.form.story.end_date.dateValidator && errors.push('End date is not a valid date. Format must be ' + moment.localeData().longDateFormat('L'));
                return errors;
            },
            endTimeErrors() {
                const errors = [];
                if (! this.$v.form.story.end_time.$dirty) return errors;
                ! this.$v.form.story.end_time.timeValidator && errors.push('Time is not valid. Format must be HH:MM');
                return errors;
            },
            featuredErrors() {
                const errors = [...this.errors.featured];
                if (! this.$v.form.story.featured.$dirty) return errors;
                ! this.$v.form.story.featured.required && errors.push('Featured priority is required');
                ! this.$v.form.story.featured.numeric && errors.push('Featured priority must be numeric');
                return errors;
            },
            featuredEndDateErrors() {
                const errors = [...this.errors.featured_end_date];
                if (! this.$v.form.story.featured_end_date.$dirty) return errors;
                ! this.$v.form.story.featured_end_date.dateValidator && errors.push('Feature end date is not a valid date. Format must be ' + moment.localeData().longDateFormat('L'));
                return errors;
            },
            featuredEndTimeErrors() {
                const errors = [];
                if (! this.$v.form.story.featured_end_time.$dirty) return errors;
                ! this.$v.form.story.featured_end_time.timeValidator && errors.push('Time is not valid. Format must be HH:MM');
                return errors;
            },
            summaryHtml() {
                if (this.form.story.summary) {
                    return marked(this.form.story.summary, { sanitize : true });
                }
                return '';
            },
            contentHtml() {
                if (this.form.story.content) {
                    return marked(this.form.story.content, { sanitize : true });
                }
                return '';
            }
        },
        validations : {
            form : {
                story : {
                    title : {
                        required
                    },
                    category : {
                        required
                    },
                    summary : {
                        required
                    },
                    content : {
                    },
                    publish_date : {
                        dateValidator
                    },
                    publish_time : {
                        timeValidator
                    },
                    end_date : {
                        dateValidator
                    },
                    end_time : {
                        timeValidator
                    },
                    remark : {
                    },
                    featured : {
                        required,
                        numeric
                    },
                    featured_end_date : {
                        dateValidator
                    },
                    featured_end_time : {
                        timeValidator
                    }
                }
            },
            step1Group : [
                'form.story.title', 'form.story.category', 'form.story.summary', 'form.story.content', 'form.story.remark'
            ],
            step2Group : [
                'form.story.publish_date', 'form.story.publish_time', 'form.story.end_date', 'form.story.end_time'
            ],
            step3Group : [
                'form.story.featured', 'form.story.featured_end_date', 'form.story.featured_end_time'
            ]
        },
        mounted() {
            this.$store.dispatch('newsModule/getCategories');
        },
        watch : {
            story(nv) {
                if (nv) {
                    this.$set(this.form.story, 'title', nv.title);
                    this.$set(this.form.story, 'category', nv.category.id);
                    this.$set(this.form.story, 'summary', nv.summary);
                    this.$set(this.form.story, 'content', nv.content);
                    this.$set(this.form.story, 'enabled', nv.enabled == 1);
                    if (nv.publish_date) {
                        this.$set(this.form.story, 'publish_date', moment(nv.publish_date, 'YYYY-MM-DD HH:mm:ss').format('L'));
                        this.$set(this.form.story, 'publish_time', moment(nv.publish_date, 'YYYY-MM-DD HH:mm:ss').format('HH:mm'));
                    }
                    if (nv.end_date) {
                        this.$set(this.form.story, 'end_date', moment(nv.end_date, 'YYYY-MM-DD HH:mm:ss').format('L'));
                        this.$set(this.form.story, 'end_time', moment(nv.end_date, 'YYYY-MM-DD HH:mm:ss').format('HH:mm'));
                    }
                    this.$set(this.form.story, 'featured', nv.featured);
                    if (nv.featured_end_date) {
                        this.$set(this.form.story, 'featured_end_date', moment(nv.featured_end_date, 'YYYY-MM-DD HH:mm:ss').format('L'));
                        this.$set(this.form.story, 'featured_end_time', moment(nv.featured_end_date, 'YYYY-MM-DD HH:mm:ss').format('HH:mm'));
                    }
                    this.$set(this.form.story, 'remark', nv.remark);
                }
            }
        },
        methods : {
            formatDate(date) {
                if (date != null) {
                    return moment(date).format('L');
                }
                return '';
            },
            allowedDates(date) {
                if (this.form.story.publish_date) {
                    return moment(this.form.story.publish_date, 'L').isSameOrBefore(date);
                }
                return false;
            },
            clear() {
                this.$v.reset();
            },
            fillModel(model) {
                model.addAttribute('title', this.form.story.title);
                model.addAttribute('summary', this.form.story.summary);
                model.addAttribute('content', this.form.story.content);
                model.addAttribute('enabled', this.form.story.enabled);
                model.addAttribute('remark', this.form.story.remark);
                model.addRelation('category', new Model('category', this.form.story.category));
                model.addAttribute('publish_date', moment(this.form.story.publish_date, 'L').format('YYYY-MM-DD') + " " + this.form.story.publish_time + ":00");
                if ( this.form.story.end_date ) {
                    var time = this.form.story.end_time;
                    if (time == null || time.length == 0) time = '00:00';
                    model.addAttribute('end_date', this.form.story.end_date.input + " " + time + ":00");
                }
                if (this.form.story.featured.length > 0 ) {
                    model.addAttribute('featured', this.form.story.featured);
                }
                if ( this.form.story.featured_end_date ) {
                    var time = this.form.story.featured_end_time;
                    if (time == null || time.length == 0) time = '00:00';
                    model.addAttribute('featured_end_date', moment(this.form.story.featured_end_date, 'YYYY-MM-DD') + " " + time + ":00");
                }
            },
            submit() {
                this.errors = initError();
                this.error = false;

                if (this.story) { // update
                    this.fillModel(this.story);
                    this.$store.dispatch('newsModule/update', this.story.serialize())
                        .then(() => {
                            console.log('update success');
                        }).catch(err => {
                          if ( err.response.status == 422 ) {
                              err.response.data.errors.forEach((item, index) => {
                                  if ( item.source && item.source.pointer ) {
                                      var attr = item.source.pointer.split('/').pop();
                                      this.errors[attr].push(item.title);
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
                } else { // create
                    var story = new Model('news');
                    this.fillModel(story);
                    this.$store.dispatch('newsModule/create', story.serialize())
                        .then(() => {
                            console.log('success');
                        }).catch(err => {
                          if ( err.response.status == 422 ) {
                              err.response.data.errors.forEach((item, index) => {
                                  if ( item.source && item.source.pointer ) {
                                      var attr = item.source.pointer.split('/').pop();
                                      this.errors[attr].push(item.title);
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
        }
    };
</script>
