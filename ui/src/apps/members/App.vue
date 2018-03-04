<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-flex xs12>
                <router-view name="MemberContent"></router-view>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import memberStore from './store';

    import messages from './lang/lang';

    export default {
        i18n : {
            messages
        },
        data() {
            return {}
        },
        computed : {
        },
        created() {
            if (!this.$store.state.memberModule) {
                this.$store.registerModule('memberModule', memberStore);
            }
            this.$store.dispatch('setSubTitle', this.$t('members'));
        }
    };
</script>
