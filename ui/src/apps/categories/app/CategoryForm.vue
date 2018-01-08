<template>
    <v-container fluid>
        <v-card class="mb-5">
            <v-card-title primary-title>
                <h4 class="headline mb-0">Category Details</h4>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-text-field name="name"
                            label="Name of the category"
                            v-model="form.category.name"
                            :error-messages="nameErrors"
                            @input="$v.form.category.name.$touch()"
                            required
                            >
                        </v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field
                            name="description"
                            v-model="form.category.description"
                            @input="$v.form.category.description.$touch"
                            :error-messages="descriptionErrors"
                            label="Description"
                            textarea>
                        </v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field
                            name="remark"
                            v-model="form.category.remark"
                            @input="$v.form.category.remark.$touch"
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
import Model from '@/js/model';

import { required } from 'vuelidate/lib/validators';

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
    props : {
        category : {
            type : Object
        }
    },
    data() {
        return {
            form : initForm(),
            errors : initError()
        }
    },
    computed : {
        error() {
            return this.$store.state.categoryModule.status.error;
        },
        nameErrors() {
            const errors = [...this.errors.name];
            if (! this.$v.form.category.name.$dirty) return errors;
            ! this.$v.form.category.name.required && errors.push('Name is required');
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
    mounted() {
        if ( this.category ) this.fillForm(this.category);
    },
    watch : {
        category(nv) {
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
            this.form.category.name = model.name;
            this.form.category.description = model.description;
            this.form.category.remark = model.remark;
        },
        fillModel(model) {
            model.addAttribute('name', this.form.category.name);
            model.addAttribute('description', this.form.category.description);
            model.addAttribute('remark', this.form.category.remark);
        },
        submit() {
            this.errors = initError();

            if (this.category) { // update
                this.fillModel(this.category);
                this.$store.dispatch('categoryModule/update', this.category.serialize())
                    .then(() => {
                        this.$router.push({ name : 'category.read', params : { id : this.category.id }});
                    }).catch(() => {
                        console.log("Error occurred in categoryModule/update");
                    });
            } else { // create
                var category = new Model('categories');
                this.fillModel(category);
                this.$store.dispatch('categoryModule/create', category.serialize())
                    .then((newCategory) => {
                        this.$router.push({ name : 'category.read', params : { id : newCategory.id }});
                    }).catch(err => {
                    });
            }
        }
    }
};

</script>
