<template>
    <div id="delete-story" uk-modal ref="dialog">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">{{ $t('delete_story')}}</h2>
            <p>
                {{ $t('are_you_sure') }}
            </p>
            <p class="uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">
                    <fa-icon name="ban" />&nbsp;&nbsp;{{ $t('cancel') }}
                </button>
                <button class="uk-button uk-button-danger" type="button" @click="ok">
                    <fa-icon name="trash" />&nbsp;&nbsp;{{ $t('delete') }}
                </button>
            </p>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/trash';
    import 'vue-awesome/icons/ban';

    import messages from './lang';

    import UIkit from 'uikit';

    export default {
        i18n : messages,
        data() {
            return {
            }
        },
        mounted() {

        },
        methods : {
            ok() {
                UIkit.modal(this.$refs.dialog).hide();
                this.$emit('deleteStoryEvent');
            }
        }
    };
</script>
