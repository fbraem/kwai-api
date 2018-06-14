<template>
    <div class="uk-margin">
        <label>
            <input :value="value"
                :id="id"
                class="uk-checkbox"
                :class="{ 'uk-form-danger' : danger }"
                type="checkbox"
                :checked="isChecked"
                @change="onChange"
                v-bind="$attrs"
            />
            <slot></slot>
        </label>
        <div v-if="errors && errors.length > 0" class="uk-text-danger uk-margin-small">
            <div v-for="error in errors">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : [
            'validator',
            'errors',
            'id',
            'value'
        ],
        computed : {
            isChecked() {
                return this.value;
            },
            danger() {
                if ( this.validator ) {
                    return this.validator.$error;
                }
                return false;
            }
        },
        methods : {
            onChange(e) {
                console.log(e.target.checked);
                this.$emit('input', e.target.checked);
                if (this.validator) this.validator.$touch();
            }
        }
    }
</script>
