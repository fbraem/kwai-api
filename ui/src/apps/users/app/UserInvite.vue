<template>
    <Page>
        <template slot="title">
            {{ $t('user_invite') }}
        </template>
        <div slot="content" class="uk-container">
            {{ token }}
        </div>
    </Page>
</template>

<script>
    import Page from './Page.vue';

    import messages from '../lang';

    export default {
        i18n : messages,
        components : {
            Page
        },
        data() {
            return {
                token : this.$route.params.token
            };
        }
    };
</script>
