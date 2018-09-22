<template>
    <div :id="id" uk-modal ref="dialog">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">
                <slot name="title"></slot>
            </h2>
            <p>
                <slot></slot>
            </p>
            <p class="uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">
                    <fa-icon name="ban" />&nbsp;&nbsp;{{ no }}
                </button>
                <button class="uk-button uk-button-danger" type="button" @click="ok">
                    <fa-icon name="trash" />&nbsp;&nbsp;{{ yes }}
                </button>
            </p>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/trash';
    import 'vue-awesome/icons/ban';

    import UIkit from 'uikit';

    export default {
        props : [
            'id',
            'no',
            'yes'
        ],
        methods : {
            ok() {
                UIkit.modal(this.$refs.dialog).hide();
                this.$emit('sure');
            }
        }
    };
</script>
