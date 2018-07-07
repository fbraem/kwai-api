<template>
    <div class="uk-margin">
        <label class="uk-form-label uk-text-bold" :class="{ 'uk-text-danger' : danger }" :for="id">
            <slot></slot>
        </label>
        <input :value="value"
            :id="id"
            class="uk-input"
            :class="{ 'uk-form-danger' : danger }"
            type="password"
            @input="onChange"
            v-bind="$attrs"
        />
        <div v-if="errors.length > 0" class="uk-text-danger uk-margin-small">
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
            danger() {
                if ( this.validator ) {
                    return this.validator.$error;
                }
                return false;
            }
        },
        methods : {
            onChange(e) {
                this.$emit('input', e.target.value);
                this.validator.$touch();
            }
        }
    }
</script>
