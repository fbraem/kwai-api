<template>
    <div>
        <v-layout>
            <v-flex xs12>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-toolbar class="elevation-0">
                            <v-icon class="fas">fa-users</v-icon>
                            <v-toolbar-title>{{ $t('users') }}</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
        <router-view name="UserContent"></router-view>
    </div>
</template>

<script>
    import userStore from '@/apps/users/store';
    import messages from './lang/App';

    export default {
        i18n : {
            messages
        },
        data() {
            return {
            };
        },
        created() {
            if (!this.$store.state.userModule) {
                this.$store.registerModule('userModule', userStore);
            }
        }
  };
</script>
