<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container">
            <div uk-grid>
                <div class="uk-width-1-1">
                    <h4 class="uk-heading-line">
                        <span>{{ $t('category') }} &ndash;
                            <template v-if="creating">{{ $t('create') }}</template>
                            <template v-else>{{ $t('update') }}</template>
                        </span>
                    </h4>
                </div>
                <div class="uk-width-1-1" uk-grid>
                    <div class="uk-width-1-1">
                        <form class="uk-form-stacked">
                            <uikit-input-text v-model="form.category.name" :validator="$v.form.category.name" :errors="nameErrors" id="name" :placeholder="$t('name_placeholder')">
                                {{ $t('name') }}:
                            </uikit-input-text>
                            <uikit-textarea v-model="form.category.description" :validator="$v.form.category.description" :rows="5" id="description" :errors="descriptionErrors" :placeholder="$t('description_placeholder')">
                                {{ $t('description') }}:
                            </uikit-textarea>
                            <uikit-textarea v-model="form.category.remark" :validator="$v.form.category.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('remark_placeholder')">
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
        </div>
    </section>
</template>

<script>
    import 'vue-awesome/icons/save';

    import Category from '@/models/Category';
    import categoryStore from '@/stores/categories';

    import { validationMixin } from 'vuelidate';
    import { required } from 'vuelidate/lib/validators';

    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitTextarea from '@/components/uikit/Textarea.vue';

    import messages from '../lang';

    var initError = function() {
        return {
            name : [],
            description : [],
            remark : []
        }
    };
    var initForm = function() {
        return {
            category : {
                name : '',
                description : '',
                remark : ''
            }
        };
    }

    export default {
        components : {
            UikitInputText,
            UikitTextarea
        },
        i18n : messages,
        mixins: [
            validationMixin
        ],
        data() {
            return {
                category : new Category(),
                form : initForm(),
                errors : initError()
            }
        },
        computed : {
            creating() {
                return this.category != null && this.category.id == null;
            },
            error() {
                return this.$store.getters['categoryModule/error'];
            },
            nameErrors() {
                const errors = [...this.errors.name];
                if (! this.$v.form.category.name.$dirty) return errors;
                ! this.$v.form.category.name.required && errors.push(this.$t('required'));
                return errors;
            },
            descriptionErrors() {
                const errors = [...this.errors.description];
                if (! this.$v.form.category.description.$dirty) return errors;
                return errors;
            },
            remarkErrors() {
                const errors = [...this.errors.remark];
                if (! this.$v.form.category.remark.$dirty) return errors;
                return errors;
            }
        },
        validations : {
            form : {
                category : {
                    name : {
                        required
                    },
                    description : {
                    },
                    remark : {
                    }
                }
            }
        },
        beforeCreate() {
            if (!this.$store.state.categoryModule) {
                this.$store.registerModule('categoryModule', categoryStore);
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
            clear() {
                this.$v.$reset();
                this.form = initForm();
            },
            async fetchData(id) {
                this.category = await this.$store.dispatch('categoryModule/read', {
                    id : id
                });
                this.fillForm();
            },
            fillForm() {
                this.form.category.name = this.category.name;
                this.form.category.description = this.category.description;
                this.form.category.remark = this.category.remark;
            },
            fillCategory() {
                this.category.name = this.form.category.name;
                this.category.description = this.form.category.description;
                this.category.remark = this.form.category.remark;
            },
            submit() {
                this.errors = initError();
                this.fillCategory();
                this.$store.dispatch('categoryModule/save', this.category)
                    .then((newCategory) => {
                        this.$router.push({ name : 'categories.read', params : { id : newCategory.id }});
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
        }
    };
</script>
