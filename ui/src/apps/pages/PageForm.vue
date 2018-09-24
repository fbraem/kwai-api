<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div class="uk-width-1-1">
                    <h4 class="uk-heading-line">
                        {{ $t('page') }} &ndash;
                        <span v-if="creating">{{ $t('create') }}</span>
                        <span v-else>{{ $t('update') }}</span>
                    </h4>
                </div>
                <div class="uk-width-1-1">
                    <form class="uk-form-stacked">
                        <uikit-checkbox v-model="form.page.enabled">
                            {{ $t('enabled') }}
                        </uikit-checkbox>
                        <uikit-select
                            v-model="form.page.category"
                            :items="categories"
                            :validator="$v.form.page.category"
                            :errors="categoryErrors"
                            id="category"
                            empty="Please select a category">
                            {{ $t('category') }}:
                        </uikit-select>
                        <uikit-textarea v-model="form.page.remark" :validator="$v.form.page.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('remark_placeholder')">
                            {{ $t('remark') }}:
                        </uikit-textarea>
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
    </section>
</template>

<script>
    import 'vue-awesome/icons/save';

    import { validationMixin } from 'vuelidate';
    import { required, numeric } from 'vuelidate/lib/validators';
    import { withParams } from 'vuelidate/lib';

    import pageStore from '@/stores/pages';
    import categoryStore from '@/stores/categories';

    import Category from '@/models/Category';
    import Content from '@/models/Content';
    import Page from '@/models/Page';

    import messages from './lang';

    import UikitCheckbox from '@/components/uikit/Checkbox.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitSelect from '@/components/uikit/Select.vue';
    import UikitTextarea from '@/components/uikit/Textarea.vue';

    var initForm = function() {
        return {
            page : {
                enabled : true,
                category : 0,
                priority : 0,
                remark : ''
            },
            content : {
                title : '',
                summary : '',
                content : '',
                remark : ''
            }
        };
    }

    var initError = function() {
        return {
            title : [],
            category : [],
            priority : [],
            summary : [],
            content : [],
            enabled : [],
            remark : []
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
            UikitCheckbox
        },
        data() {
            return {
                page : new Page(),
                form : initForm(),
                errors : initError()
            }
        },
        computed : {
            creating() {
                return this.page.id == null;
            },
            error() {
                return this.$store.state.pageModule.status.error;
            },
            categories() {
                return this.$store.state.categoryModule.categories.map((category) => ({value : category.id, text : category.name }));
            },
            dateFormat() {
                return 'Format ' + moment.localeData().longDateFormat('L');
            },
            titleErrors() {
                const errors = [...this.errors.title];
                if (! this.$v.form.content.title.$dirty) return errors;
                ! this.$v.form.content.title.required && errors.push('Title is required');
                return errors;
            },
            categoryErrors() {
                const errors = [...this.errors.category];
                if (! this.$v.form.page.category.$dirty) return errors;
                ! this.$v.form.page.category.required && errors.push('Category is required');
                return errors;
            },
            priorityErrors() {
                const errors = [...this.errors.priority];
                if (! this.$v.form.page.priority.$dirty) return errors;
                ! this.$v.form.page.priority.required && errors.push('Priority is required');
                ! this.$v.form.page.priority.numeric && errors.push('Priority must be numeric');
                return errors;
            },
            summaryErrors() {
                const errors = [...this.errors.summary];
                if (! this.$v.form.content.summary.$dirty) return errors;
                ! this.$v.form.content.summary.required && errors.push('Summary is required');
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
        },
        validations : {
            form : {
                page : {
                    category : {
                        required
                    },
                    priority : {
                        required,
                        numeric
                    },
                    remark : {
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
                    },
                }
            }
        },
        beforeCreate() {
            if (!this.$store.state.pageModule) {
                this.$store.registerModule('pageModule', pageStore);
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
                this.page = await this.$store.dispatch('pageModule/read', {
                    id : id
                });
                this.fillForm();
            },
            clear() {
                this.$v.$reset();
                this.form = initForm();
            },
            fillForm() {
                this.form.page.enabled = this.page.enabled == 1;
                this.form.page.remark = this.page.remark;
                this.form.page.category = this.page.category.id;
                this.form.page.priority = this.page.priority;
                this.form.content.summary = this.page.contents[0].summary;
                this.form.content.content = this.page.contents[0].content;
                this.form.content.title = this.page.contents[0].title;
            },
            fillPage() {
                this.page.enabled = this.form.page.enabled;
                this.page.remark = this.form.page.remark;
                this.page.category = new Category();
                this.page.category.id = this.form.page.category;
                this.page.priority = this.form.page.priority;
            },
            fillContent(content) {
                content.title = this.form.content.title;
                content.summary = this.form.content.summary;
                content.content = this.form.content.content;
                if (this.page && this.page.contents && this.page.contents.length > 0) content.id = this.page.contents[0].id;
            },
            submit() {
                this.errors = initError();
                this.fillPage();
                this.$store.dispatch('pageModule/save', this.page)
                    .then((newPage) => {
                        this.page.id = newPage.id;
                        var content = new Content();
                        this.fillContent(content);
                        this.$store.dispatch('pageModule/attachContent', {
                            page : this.page,
                            content : content
                        }).then((newPage) => {
                            this.$router.push({ name : 'pages.read', params : { id : this.page.id }});
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
