<template>
    <div class="uk-container">
        <div v-if="loading" class="uk-flex-center" uk-grid>
            <div class="uk-text-center">
                <fa-icon name="spinner" scale="2" spin />
            </div>
        </div>
        <div v-else uk-grid>
            <div v-for="user in users" :key="user.id" class="uk-width-1-2@m">
                <UserCard :user="user"></UserCard>
            </div>
        </div>
    </div>
</template>

<script>
    import 'vue-awesome/icons/envelope';
    import 'vue-awesome/icons/spinner';
    import 'vue-awesome/icons/ellipsis-h';

    import messages from '../lang';

    import UserCard from '../components/UserCard.vue';

    export default {
        i18n : messages,
        components : {
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
