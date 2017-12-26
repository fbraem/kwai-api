<template>
    <v-container fluid>
        <v-stepper v-model="stepper" non-linear>
            <v-stepper-header>
                <v-stepper-step step="1" :complete="!$v.step1Group.$invalid" editable>Enter the story</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step step="2" :complete="!$v.step2Group.$invalid" :editable="!$v.step1Group.$invalid">Publish Information</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step step="3" :complete="!$v.step3Group.$invalid" :editable="!$v.step1Group.$invalid && !$v.step2Group.$invalid">Featured Information</v-stepper-step>
                <v-divider></v-divider>
                <v-stepper-step step="4" :complete="true" :editable="true">Image</v-stepper-step>
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
                                <v-text-field
                                    name="summary"
                                    v-model="form.story.summary"
                                    @input="$v.form.story.summary.$touch"
                                    :error-messages="summaryErrors"
                                    label="Summary"
                                    textarea
                                    required>
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field
                                    name="content"
                                    v-model="form.story.content"
                                    @input="$v.form.story.content.$touch"
                                    :error-messages="contentErrors"
                                    label="Content"
                                    textarea>
                                </v-text-field>
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
                                :$v="$v.form.story.publish_date"
                            />
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
                            />
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
                                />
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
            <v-stepper-content step="4">
                <v-flex xs12>
                    <v-layout row justify-center>
                        <v-flex xs2>
                            <v-btn @click="upload">Upload</v-btn>
                        </v-flex>
                    </v-layout>
                </v-flex>
                <v-layout row wrap>
                    <v-flex xs12>
                        <div class="headline">Overview Image</div>
                        <p>
                            This is the image that will be shown in the header on the overview page
                            or on small screens.
                        </p>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex xs12>
                        <VueCroppie v-model="imageOverviewCrop" :url="imageOverviewURL" @result="cropOverviewResult" :boundary="{ height : 400 }" :viewport="{ width: 333, height : 200 }"/>
                    </v-flex>
                    <v-flex xs12>
                        <v-layout justify-center>
                            <img class="text-xs-center" v-if="imageOverviewPreview" :src="imageOverviewPreview" />
                        </v-layout>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex xs12>
                        <div class="headline">details Image</div>
                        <p>
                            This is the image that will be shown in the header on story page.
                        </p>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex xs12>
                        <VueCroppie v-model="imageDetailCrop" :url="imageDetailURL" @result="cropDetailResult" :boundary="{ height : 600 }" :viewport="{ width: 800, height : 400 }"/>
                    </v-flex>
                    <v-flex xs12>
                        <v-layout justify-center>
                            <img class="text-xs-center" v-if="imageDetailPreview" :src="imageDetailPreview" />
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-stepper-content>
        </v-stepper>
        <v-btn color="primary" :disabled="$v.$invalid" @click="submit">Submit</v-btn>
        <v-btn flat @click="clear">Clear</v-btn>
        <form enctype="multipart/form-data" novalidate>
            <input type="file" accept="image/*" ref="fileInput" @change="onFileChange"/>
        </form>
    </v-container>
</template>

<style scoped>
    input[type=file] {
        position: absolute;
        left: -99999px;
    }
</style>


<script>
    import Model from '@/js/model';
    import moment from 'moment';
    import 'moment-timezone';

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

    import DateField from '@/components/DateField.vue';
    import VueCroppie from "@/components/Croppie.vue";

    var initForm = function() {
        return {
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
        };
    }

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
            DateField,
            VueCroppie
        },
        data() {
            return {
                form : initForm(),
                errors : initError(),
                stepper : 1,
                imageOverviewCrop : null,
                imageOverviewURL : null,
                imageOverviewPreview : null,
                imageDetailCrop : null,
                imageDetailURL : null,
                imageDetailPreview : null
            }
        },
        computed : {
            error() {
                return this.$store.state.newsModule.status.error;
            },
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
                ! this.$v.form.story.summary.required && errors.push('Summary is required');
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
            this.$store.dispatch('newsModule/getCategories').
                then(() => {
                    if ( this.story ) this.fillForm(this.story);
                });
        },
        watch : {
            image(nv) {
            },
            story(nv) {
                if (nv) {
                    this.fillForm(nv);
                }
            },
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
            cropDetailResult(result) {
                this.imageDetailPreview = result;
            },
            cropOverviewResult(result) {
                this.imageOverviewPreview = result;
            },
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
                this.$v.$reset();
                this.form = initForm();
            },
            fillForm(model) {
                this.form.story.title = model.contents[0].title;
                this.form.story.category = model.category.id;
                this.form.story.summary = model.contents[0].summary;
                this.form.story.content = model.contents[0].content;
                this.form.story.enabled = model.enabled == 1;
                if (model.publish_date) {
                    var utc = moment.utc(model.publish_date, 'YYYY-MM-DD HH:mm:ss');
                    var publishDate = utc.local();
                    this.form.story.publish_date = moment(publishDate).format('L');
                    this.form.story.publish_time = moment(publishDate).format('HH:mm');
                }
                if (model.end_date) {
                    var utc = moment.utc(model.end_date, 'YYYY-MM-DD HH:mm:ss');
                    var endDate = utc.local();
                    this.form.story.end_date = moment(endDate, 'YYYY-MM-DD HH:mm:ss').format('L');
                    this.form.story.end_time = moment(endDate, 'YYYY-MM-DD HH:mm:ss').format('HH:mm');
                }
                this.form.story.featured = model.featured;
                if (model.featured_end_date) {
                    var utc = moment.utc(model.featured_end_date, 'YYYY-MM-DD HH:mm:ss');
                    var featuredEndDate = utc.local();
                    this.form.story.featured_end_date = moment(featuredEndDate, 'YYYY-MM-DD HH:mm:ss').format('L');
                    this.form.story.featured_end_time = moment(featuredEndDate, 'YYYY-MM-DD HH:mm:ss').format('HH:mm');
                }
                this.form.story.remark = model.remark;
            },
            fillModel(model) {
                model.addAttribute('title', this.form.story.title);
                model.addAttribute('summary', this.form.story.summary);
                model.addAttribute('content', this.form.story.content);
                model.addAttribute('enabled', this.form.story.enabled);
                model.addAttribute('remark', this.form.story.remark);
                model.addRelation('category', new Model('category', this.form.story.category));
                model.addAttribute('publish_date', moment(moment(this.form.story.publish_date, 'L').format('YYYY-MM-DD') + " " + this.form.story.publish_time + ":00").utc().format('YYYY-MM-DD HH:mm:ss'));
                model.addAttribute('publish_date_timezone', moment.tz.guess());
                if ( this.form.story.end_date ) {
                    var time = this.form.story.end_time;
                    if (time == null || time.length == 0) time = '00:00';
                    model.addAttribute('end_date', moment(moment(this.form.story.end_date, 'L').format('YYYY-MM-DD') + " " + time + ":00").utc().format('YYYY-MM-DD HH:mm:ss'));
                    model.addAttribute('end_date_timezone', moment.tz.guess());
                }
                if (this.form.story.featured.length > 0 ) {
                    model.addAttribute('featured', this.form.story.featured);
                }
                if ( this.form.story.featured_end_date ) {
                    var time = this.form.story.featured_end_time;
                    if (time == null || time.length == 0) time = '00:00';
                    model.addAttribute('featured_end_date', moment(moment(this.form.story.featured_end_date, 'L').format('YYYY-MM-DD') + " " + time + ":00").utc().format('YYYY-MM-DD HH:mm:ss'));
                    model.addAttribute('featured_date_timezone', moment.tz.guess());
                }
            },
            submit() {
                if (this.imageOverviewCrop) {
                    var formData = new FormData();
                    formData.append('image', this.$refs.fileInput.files[0]);
                    formData.append('overview_x', this.imageOverviewCrop.points[0]);
                    formData.append('overview_y', this.imageOverviewCrop.points[1]);
                    formData.append('overview_width', this.imageOverviewCrop.points[2] - this.imageOverviewCrop.points[0]);
                    formData.append('overview_height', this.imageOverviewCrop.points[3] - this.imageOverviewCrop.points[1]);
                    formData.append('overview_scale', this.imageOverviewCrop.zoom);
                    formData.append('detail_x', this.imageDetailCrop.points[0]);
                    formData.append('detail_y', this.imageDetailCrop.points[1]);
                    formData.append('detail_width', this.imageDetailCrop.points[2] - this.imageDetailCrop.points[0]);
                    formData.append('detail_height', this.imageDetailCrop.points[3] - this.imageDetailCrop.points[1]);
                    formData.append('detail_scale', this.imageDetailCrop.zoom);
                    this.$store.dispatch('newsModule/uploadImage', {
                        story : {
                            id : this.story.id
                        },
                        formData : formData
                    });
                }
                this.errors = initError();

                if (this.story) { // update
                    this.fillModel(this.story);
                    this.$store.dispatch('newsModule/update', this.story.serialize())
                        .then(() => {
                            this.$router.push('/read/' + this.story.id);
                        }).catch(err => {
                            console.log(err);
                        });
                } else { // create
                    var story = new Model('news_stories');
                    this.fillModel(story);
                    this.$store.dispatch('newsModule/create', story.serialize())
                        .then((newStory) => {
                            this.$router.push('/read/' + newStory.id);
                        }).catch(err => {
                            console.log(err);
                        });
                }
            },
            upload() {
                this.$refs.fileInput.click();
            },
            onFileChange($event) {
                const files = $event.target.files || $event.dataTransfer.files
                if (files) {
                    if (files.length > 0) {
                        this.filename = [...files].map(file => file.name).join(', ');
                    } else {
                        this.filename = null;
                    }
                } else {
                    this.filename = $event.target.value.split('\\').pop();
                }
                var reader = new FileReader();
                reader.onload = (e) => {
                    this.imageOverviewURL = e.target.result;
                    this.imageDetailURL = e.target.result;
                };
                reader.readAsDataURL(files[0]);
            }
        }
    };
</script>
