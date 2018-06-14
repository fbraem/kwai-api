<template>
    <div class="uk-margin">
        <label class="uk-form-label uk-text-bold" :class="{ 'uk-text-danger' : danger }" :for="id">
            <slot></slot>
        </label>
        <select :value="value"
            :id="id"
            class="uk-select"
            :class="{ 'uk-form-danger' : danger }"
            @change="onChange"
            v-bind="$attrs">
            <option value="" v-if="empty">{{ empty}}</option>
            <option v-for="item in items" :value="item.value">
                {{ item.text }}
            </option>
        </select>
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
            'value',
            'empty',
            'items'
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
