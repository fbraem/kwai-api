<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div class="uk-width-1-1">
                    <h4 class="uk-heading-line">
                        {{ $t('news') }} &ndash;
                        <span v-if="creating">{{ $t('create') }}</span>
                        <span v-else>{{ $t('update') }}</span>
                    </h4>
                </div>
                <div uk-grid>
                    <div class="uk-width-1-1">
                        <form class="uk-form-stacked">
                            <uikit-checkbox v-model="form.story.enabled">
                                {{ $t('enabled') }}
                            </uikit-checkbox>
                            <uikit-select
                                v-model="form.story.category"
                                :items="categories"
                                :validator="$v.form.story.category"
                                :errors="categoryErrors"
                                id="category"
                                empty="Please select a category">
                                {{ $t('category') }}:
                            </uikit-select>
                            <div uk-grid>
                                <div class="uk-width-1-2">
                                    <uikit-input-text v-model="form.story.publish_date" :validator="$v.form.story.publish_date" :errors="publishDateErrors" id="publish_date" :placeholder="$t('publish_date_placeholder', { format : dateFormat })">
                                        {{ $t('publish_date') }}:
                                    </uikit-input-text>
                                </div>
                                <div class="uk-width-1-2">
                                    <uikit-input-text v-model="form.story.publish_time" :validator="$v.form.story.publish_time" :errors="publishTimeErrors" id="publish_time" :placeholder="$t('publish_time_placeholder', { format :'(HH:MM)' })">
                                        {{ $t('publish_time') }}:
                                    </uikit-input-text>
                                </div>
                            </div>
                            <div uk-grid>
                                <div class="uk-width-1-2">
                                    <uikit-input-text v-model="form.story.end_date" :validator="$v.form.story.end_date" :errors="endDateErrors" id="end_date" :placeholder="$t('end_date_placeholder', { format : dateFormat })">
                                        {{ $t('end_date') }}:
                                    </uikit-input-text>
                                </div>
                                <div class="uk-width-1-2">
                                    <uikit-input-text v-model="form.story.end_time" :validator="$v.form.story.end_time" :errors="endTimeErrors" id="end_time" :placeholder="$t('end_time_placeholder', { format :'(HH:MM)' })">
                                        {{ $t('end_time') }}:
                                    </uikit-input-text>
                                </div>
                            </div>
                            <uikit-textarea v-model="form.story.remark" :validator="$v.form.story.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('remark_placeholder')">
                                {{ $t('remark') }}:
                            </uikit-textarea>
                            <div uk-grid>
                                <div class="uk-width-1-1">
                                    <div class="uk-tile uk-tile-default uk-tile-muted uk-padding-small">
                                        <div uk-grid>
                                            <div class="uk-width-1-1">
                                                <h3>{{ $t('featured') }}</h3>
                                                <blockquote class="uk-text-meta">
                                                    {{ $t('featured_hint') }}
                                                </blockquote>
                                            </div>
                                            <div class="uk-width-1-1">
                                                <div uk-grid>
                                                    <div class="uk-width-expand">
                                                        <uikit-range v-model="form.story.featured" :validator="$v.form.story.featured" :errors="featuredErrors" id="featured">
                                                            {{ $t('featured_priority') }}
                                                        </uikit-range>
                                                    </div>
                                                    <div>
                                                        {{ form.story.featured }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="uk-width-1-2">
                                                <uikit-input-text v-model="form.story.featured_end_date" :validator="$v.form.story.featured_end_date" :errors="featuredEndDateErrors" id="featured_end_date" :placeholder="$t('featured_end_date_placeholder', { format : dateFormat })">
                                                    {{ $t('featured_end_date') }}:
                                                </uikit-input-text>
                                            </div>
                                            <div class="uk-width-1-2">
                                                <uikit-input-text v-model="form.story.featured_end_time" :validator="$v.form.story.featured_end_time" :errors="featuredEndTimeErrors" id="featured_end_time" :placeholder="$t('featured_end_time_placeholder', { format :'(HH:MM)' })">
                                                    {{ $t('featured_end_time') }}:
                                                </uikit-input-text>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="uk-width-1-1">
                        <form class="uk-form-stacked">
                            <uikit-input-text v-model="form.content.title" :validator="$v.form.content.title" :errors="titleErrors" id="title" :placeholder="$t('title_placeholder')">
                                {{ $t('title') }}:
                            </uikit-input-text>
                            <uikit-textarea v-model="form.content.summary" :validator="$v.form.content.summary" :rows="5" id="summary" :errors="summaryErrors" :placeholder="$t('summary_placeholder')">
                                {{ $t('summary') }}:
                            </uikit-textarea>
                            <uikit-textarea v-model="form.content.content" :validator="$v.form.content.content" :rows="10" id="content" :errors="contentErrors" :placeholder="$t('content_placeholder')">
                                {{ $t('content') }}:
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
        </div>
    </section>
</template>

<style scoped>
    input[type=file] {
        position: absolute;
        left: -99999px;
    }
</style>

<script>
    import 'vue-awesome/icons/save';

    import moment from 'moment';
    import 'moment-timezone';

    import { validationMixin } from 'vuelidate';

    import messages from '../lang';

    import newsStore from '../store';
    import categoryStore from '@/apps/categories/store';

    import Category from '@/apps/categories/models/Category';
    import Content from '@/apps/contents/models/Content';
    import Story from '../models/Story';

    import { required, numeric, and } from 'vuelidate/lib/validators';
    import { withParams } from 'vuelidate/lib';

    const timeValidator = withParams({ type : 'time' }, (value) => {
        if ( value != null ) return /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/.test(value);
        return true;
    });

    const isDate = (value) => {
        return moment(value, "L", true).isValid();
    };
    const isTime = (value) => {
        return /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/.test(value);
    };

    const createDatetime = (date, time) => {
        if (time == null || time.length == 0) {
            time = '00:00';
        }
        date += ' ' + time;
        return moment(date, 'L HH:mm', true);
    }

    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitSelect from '@/components/uikit/Select.vue';
    import UikitTextarea from '@/components/uikit/Textarea.vue';
    import UikitCheckbox from '@/components/uikit/Checkbox.vue';
    import UikitRange from '@/components/uikit/Range.vue';

    var initForm = function() {
        return {
            story : {
                category : null,
                enabled : false,
                publish_date : moment().format('L'),
                publish_time : moment().format('HH:mm'),
                end_date : null,
                end_time : null,
                featured : 0,
                featured_end_date : null,
                featured_end_time : null,
                remark : ''
            },
            content : {
                //TODO: in the future allow to add multiple contents
                title : '',
                summary : '',
                content : ''
            }
        };
    }

    var initError = function() {
        return {
            category : [],
            enabled : [],
            publish_date : [],
            end_date : [],
            remark : [],
            featured : [],
            featured_end_date : [],
            title : [],
            summary : [],
            content : []
        }
    };

    export default {
        i18n : messages,
        mixins: [
            validationMixin
        ],
        components : {
            UikitInputText,
            UikitSelect,
            UikitTextarea,
            UikitCheckbox,
            UikitRange
        },
        data() {
            return {
                story : new Story(),
                form : initForm(),
                errors : initError()
            }
        },
        computed : {
            creating() {
                return this.story != null && this.story.id == null;
            },
            error() {
                return this.$store.state.newsModule.status.error;
            },
            categories() {
                return this.$store.state.categoryModule.categories.map((category) => ({value : category.id, text : category.name }));
            },
            dateFormat() {
                return '(' + moment.localeData().longDateFormat('L') + ')';
            },
            categoryErrors() {
                const errors = [...this.errors.category];
                if (! this.$v.form.story.category.$dirty) return errors;
                ! this.$v.form.story.category.required && errors.push(this.$t('required'));
                return errors;
            },
            remarkErrors() {
                const errors = this.errors.remark;
                return errors;
            },
            publishDatetime() {
                return createDatetime(this.form.story.publish_date, this.form.story.publish_time);
            },
            publishDateErrors() {
                var errors = [...this.errors.publish_date];
                if (! this.$v.form.story.publish_date.$dirty) return errors;
                ! this.$v.form.story.publish_date.required && errors.push(this.$t('required'));
                if (!this.$v.form.story.publish_date.date) errors.push(this.$t('invalid_date', { format : moment.localeData().longDateFormat('L') }));
                return errors;
            },
            publishTimeErrors() {
                const errors = [];
                if (! this.$v.form.story.publish_time.$dirty) return errors;
                ! this.$v.form.story.publish_time.required && errors.push(this.$t('required'));
                ! this.$v.form.story.publish_time.time && errors.push(this.$t('invalid_time', { format : 'HH:MM'}));
                return errors;
            },
            endDatetime() {
                return createDatetime(this.form.story.end_date, this.form.story.end_time);
            },
            endDateErrors() {
                const errors = [...this.errors.end_date];
                if (! this.$v.form.story.end_date.$dirty) return errors;
                ! this.$v.form.story.end_date.date && errors.push(this.$t('invalid_date', { format : moment.localeData().longDateFormat('L') }));
                ! this.$v.form.story.end_date.after && errors.push(this.$t('invalid_end_date'));
                return errors;
            },
            endTimeErrors() {
                const errors = [];
                if (! this.$v.form.story.end_time.$dirty) return errors;
                ! this.$v.form.story.end_time.time && errors.push(this.$t('invalid_time', { format : 'HH:MM'}));
                return errors;
            },
            featuredErrors() {
                const errors = [...this.errors.featured];
                if (! this.$v.form.story.featured.$dirty) return errors;
                ! this.$v.form.story.featured.required && errors.push(this.$t('required'));
                ! this.$v.form.story.featured.numeric && errors.push(this.$t('invalid_numeric'));
                return errors;
            },
            featuredEndDatetime() {
                return createDatetime(this.form.story.featured_end_date, this.form.story.featured_end_time);
            },
            featuredEndDateErrors() {
                const errors = [...this.errors.featured_end_date];
                if (! this.$v.form.story.featured_end_date.$dirty) return errors;
                ! this.$v.form.story.featured_end_date.date && errors.push(this.$t('invalid_date', { format : moment.localeData().longDateFormat('L') }));
                ! this.$v.form.story.featured_end_date.between && errors.push(this.$t('invalid_featured_date'));
                return errors;
            },
            featuredEndTimeErrors() {
                const errors = [];
                if (! this.$v.form.story.featured_end_time.$dirty) return errors;
                ! this.$v.form.story.featured_end_time.time && errors.push(this.$t('invalid_time', { format : 'HH:MM'}));
                return errors;
            },
            titleErrors() {
                const errors = [...this.errors.title];
                if (! this.$v.form.content.title.$dirty) return errors;
                ! this.$v.form.content.title.required && errors.push(this.$t('required'));
                return errors;
            },
            summaryErrors() {
                const errors = [...this.errors.summary];
                if (! this.$v.form.content.summary.$dirty) return errors;
                ! this.$v.form.content.summary.required && errors.push(this.$t('required'));
                return errors;
            },
            contentErrors() {
                const errors = this.errors.content;
                return errors;
            }
        },
        validations : {
            form : {
                story : {
                    category : {
                        required
                    },
                    publish_date : {
                        required,
                        date(value) {
                            if (value) {
                                return isDate(value);
                            }
                            return true;
                        }
                    },
                    publish_time : {
                        required,
                        time(value) {
                            if (value) return isTime(value);
                            return true;
                        }
                    },
                    end_date : {
                        date(value) {
                            if (value) {
                                return isDate(value);
                            }
                            return true;
                        },
                        after(value) {
                            if (isDate(value) && this.publishDatetime.isValid() && this.endDatetime.isValid()) {
                                return this.publishDatetime.isSameOrBefore(this.endDatetime);
                            }
                            return true;
                        }
                    },
                    end_time : {
                        time(value) {
                            if (value) return isTime(value);
                            return true;
                        }
                    },
                    remark : {
                    },
                    featured : {
                        required,
                        numeric
                    },
                    featured_end_date : {
                        date(value) {
                            if (value) {
                                return isDate(value);
                            }
                            return true;
                        },
                        between(value) {
                            if (isDate(value) && this.publishDatetime.isValid() && this.featuredEndDatetime.isValid()) {
                                if (this.endDatetime.isValid()) {
                                    return this.featuredEndDatetime.between(this.publishDatetime, this.endDatetime);
                                }
                                return this.publishDatetime.isSameOrBefore(this.featuredEndDatetime);
                            }
                            return true;
                        }
                    },
                    featured_end_time : {
                        time(value) {
                            if (value) return isTime(value);
                            return true;
                        }
                    }
                },
                content : {
                    title : {
                        required
                    },
                    summary : {
                        required
                    },
                    content : {
                    }
                }
            }
        },
        beforeCreate() {
            if (!this.$store.state.newsModule) {
                this.$store.registerModule('newsModule', newsStore);
            }
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
            }
        },
        created() {
            this.$store.dispatch('categoryModule/browse').then(() => {
                if (this.$route.params.id) {
                    this.fetchData(this.$route.params.id);
                }
            });
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
                this.story = await this.$store.dispatch('newsModule/read', {
                    id : id
                });
                this.fillForm();
            },
            clear() {
                this.$v.$reset();
                this.form = initForm();
            },
            fillForm() {
                this.form.story.category = this.story.category.id;
                this.form.story.enabled = this.story.enabled == 1;
                if (this.story.publish_date) {
                    this.form.story.publish_date = this.story.localPublishDate;
                    this.form.story.publish_time = this.story.localPublishTime;
                }
                if (this.story.end_date) {
                    this.form.story.end_date = this.story.localEndDate;
                    this.form.story.end_time = this.story.localEndTime;
                }
                this.form.story.featured = this.story.featured;
                if (this.story.featured_end_date) {
                    this.form.story.featured_end_date = this.story.localFeaturedEndDate;
                    this.form.story.featured_end_time = this.story.localFeaturedEndTime;
                }
                this.form.story.remark = this.story.remark;
                if (this.story.contents && this.story.contents.length > 0) {
                    this.form.content.title = this.story.contents[0].title;
                    this.form.content.summary = this.story.contents[0].summary;
                    this.form.content.content = this.story.contents[0].content;
                }
            },
            fillStory() {
                var tz = moment.tz.guess();
                this.story.enabled = this.form.story.enabled;
                this.story.remark = this.form.story.remark;
                this.story.category = new Category();
                this.story.category.id = this.form.story.category;
                this.story.publish_date = this.publishDatetime.utc();
                this.story.publish_date_timezone = tz;
                if ( this.form.story.end_date ) {
                    this.story.end_date = this.endDatetime.utc();
                    this.story.end_date_timezone = tz;
                }
                this.story.featured = this.form.story.featured;
                if ( this.form.story.featured_end_date ) {
                    this.story.featured_end_date = this.featuredEndDatetime.utc();
                    this.story.featured_date_timezone = tz;
                }
            },
            fillContent(content) {
                content.title = this.form.content.title;
                content.summary = this.form.content.summary;
                content.content = this.form.content.content;
                if (this.story && this.story.contents && this.story.contents.length > 0) content.id = this.story.contents[0].id;
            },
            submit() {
                this.errors = initError();
                this.fillStory(this.story);
                this.$store.dispatch('newsModule/save', this.story)
                    .then((newStory) => {
                        this.story.id = newStory.id;
                        var content = new Content();
                        this.fillContent(content);
                        this.$store.dispatch('newsModule/attachContent', {
                            story : this.story,
                            content : content
                        }).then((newStory) => {
                            this.$router.push({ name : 'news.story', params : { id : this.story.id }});
                        }).catch((err) => {
                            console.log(err);
                        })
                        ;
                    }).catch(err => {
                        console.log(err);
                    });
            }
        }
    };
</script>
