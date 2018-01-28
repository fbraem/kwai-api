<template>
    <v-container fluid>
        <v-card class="mb-5">
            <v-card-title primary-title>
                <h4 class="headline mb-0">Page Details</h4>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-text-field name="title"
                            label="Title of the page"
                            v-model="form.page.title"
                            :error-messages="titleErrors"
                            @input="$v.form.page.title.$touch()"
                            required
                            >
                        </v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-select
                            :items="categories"
                            v-model="form.page.category"
                            @input="$v.form.page.category.$touch"
                            :error-messages="categoryErrors"
                            label="Category"
                            required>
                        </v-select>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field style="font-family:monospace"
                            name="summary"
                            v-model="form.page.summary"
                            @input="$v.form.page.summary.$touch"
                            :error-messages="summaryErrors"
                            label="Summary"
                            textarea
                            required>
                        </v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field style="font-family:monospace"
                            name="content"
                            v-model="form.page.content"
                            @input="$v.form.page.content.$touch"
                            :error-messages="contentErrors"
                            label="Content"
                            textarea>
                        </v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field
                            name="remark"
                            v-model="form.page.remark"
                            @input="$v.form.page.remark.$touch"
                            :error-messages="remarkErrors"
                            label="Remark"
                            textarea
                            hint="Enter a remark about this page">
                        </v-text-field>
                    </v-flex>
                </v-layout>
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
                            This is the image that will be shown in the header on the page.
                        </p>
                    </v-flex>
                </v-layout>
                <v-layout row wrap>
                    <v-flex xs12>
                        <VueCroppie v-model="imageCrop" :url="imageURL" @result="cropResult" :boundary="{ height : 600 }" :viewport="{ width: 800, height : 400 }"/>
                    </v-flex>
                    <v-flex xs12>
                        <v-layout justify-center>
                            <img class="text-xs-center" v-if="imagePreview" :src="imagePreview" />
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
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

    import VueCroppie from "@/components/Croppie.vue";

    var initForm = function() {
        return {
            page : {
                title : '',
                category : 0,
                summary : '',
                content : '',
                enabled : true,
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
            remark : []
        }
    };

    export default {
        props : {
            page : {
                type : Object
            }
        },
        components : {
            VueCroppie
        },
        data() {
            return {
                form : initForm(),
                errors : initError(),
                imageCrop : null,
                imageURL : null,
                imagePreview : null
            }
        },
        computed : {
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
                if (! this.$v.form.page.title.$dirty) return errors;
                ! this.$v.form.page.title.required && errors.push('Title is required');
                return errors;
            },
            categoryErrors() {
                const errors = [...this.errors.category];
                if (! this.$v.form.page.category.$dirty) return errors;
                ! this.$v.form.page.category.required && errors.push('Category is required');
                return errors;
            },
            summaryErrors() {
                const errors = [...this.errors.summary];
                if (! this.$v.form.page.summary.$dirty) return errors;
                ! this.$v.form.page.summary.required && errors.push('Summary is required');
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
                    remark : {
                    }
                }
            }
        },
        mounted() {
            this.$store.dispatch('categoryModule/browse').
                then(() => {
                    if ( this.page ) this.fillForm(this.page);
                });
        },
        watch : {
            image(nv) {
            },
            page(nv) {
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
            cropResult(result) {
                this.imagePreview = result;
            },
            formatDate(date) {
                if (date != null) {
                    return moment(date).format('L');
                }
                return '';
            },
            clear() {
                this.$v.$reset();
                this.form = initForm();
            },
            fillForm(model) {
                this.form.page.title = model.contents[0].title;
                this.form.page.category = model.category.id;
                this.form.page.summary = model.contents[0].summary;
                this.form.page.content = model.contents[0].content;
                this.form.page.enabled = model.enabled == 1;
                this.form.page.remark = model.remark;
            },
            fillModel(model) {
                model.addAttribute('title', this.form.page.title);
                model.addAttribute('summary', this.form.page.summary);
                model.addAttribute('content', this.form.page.content);
                model.addAttribute('enabled', this.form.page.enabled);
                model.addAttribute('remark', this.form.page.remark);
                model.addRelation('category', new Model('category', this.form.page.category));
            },
            submit() {
                this.errors = initError();

                if (this.page) { // update
                    this.fillModel(this.page);
                    this.$store.dispatch('pageModule/update', this.page.serialize())
                        .then(() => {
                            if (this.imageURL) {
                                this.uploadImage(this.page.id)
                                    .then(() => {
                                        this.$router.push({ name : 'pages.read', params : { id : this.page.id }});
                                    }).catch(err => {
                                        console.log(err);
                                    });
                            } else {
                                this.$router.push({ name : 'pages.read', params : { id : this.page.id }});
                            }
                        }).catch(err => {
                            console.log(err);
                        });
                } else { // create
                    var page = new Model('pages');
                    this.fillModel(page);
                    this.$store.dispatch('pageModule/create', page.serialize())
                        .then((newPage) => {
                            if (this.imageURL) {
                                this.uploadImage(newPage.id)
                                    .then(() => {
                                        this.$router.push({ name : 'pages.read', params : { id : newPage.id }});
                                    }).catch(err => {
                                        console.log(err);
                                    });
                            } else {
                                this.$router.push({ name : 'pages.read', params : { id : newPage.id }});
                            }
                        }).catch(err => {
                            console.log(err);
                        });
                }
            },
            uploadImage(pageId) {
                var formData = new FormData();
                formData.append('image', this.$refs.fileInput.files[0]);
                formData.append('x', this.imageCrop.points[0]);
                formData.append('y', this.imageCrop.points[1]);
                formData.append('width', this.imageCrop.points[2] - this.imageCrop.points[0]);
                formData.append('height', this.imageCrop.points[3] - this.imageCrop.points[1]);
                formData.append('scale', this.imageCrop.zoom);
                return this.$store.dispatch('pageModule/uploadImage', {
                    page : {
                        id : pageId
                    },
                    formData : formData
                });
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
                    this.imageURL = e.target.result;
                };
                reader.readAsDataURL(files[0]);
            }
        }
    };
</script>
