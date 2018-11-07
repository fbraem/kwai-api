<template>
    <Page>
        <template slot="title">
            {{ $t('user_mgmt') }}
        </template>
        <div slot="content" class="uk-container">
            <div v-if="loading" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class="fas fa-spinner fa-2x fa-spin"></i>
                </div>
            </div>
            <div v-else uk-grid>
                <div v-for="user in users" :key="user.id" class="uk-width-1-2@m">
                    <UserCard :user="user"></UserCard>
                </div>
            </div>
        </div>
    </Page>
</template>

<script>
    import messages from './lang';

    import Page from './Page.vue';
    import UserCard from './components/UserCard.vue';

    import userStore from '@/stores/users';

    export default {
        i18n : messages,
        components : {
            Page,
            UserCard
        },
        computed : {
            loading() {
                return this.$store.getters['userModule/loading'];
            },
            noAvatarImage() {
                return require('@/apps/users/images/no_avatar.png');
            },
            users() {
                return this.$store.getters['userModule/users'];
            },
            userLink() {
                return {
                    name : 'users.read',
                    params : {
                        id : id
                    }
                };
            }
        },
        created() {
            if (!this.$store.state.userModule) {
                this.$store.registerModule('userModule', userStore);
            }
        },
        mounted() {
            this.fetchData();
        },
        methods : {
            fetchData() {
                this.$store.dispatch('userModule/browse')
                    .catch((error) => {
                        console.log(error);
                        if (error.response && error.response.status == 401) { // Not authorized
                            //TODO: show an alert?
                        }
                    });
            }
        }
    };
</script>
