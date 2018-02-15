<template>
    <v-container fluid>
        <v-layout row wrap>
            <v-card v-for="user in users" :key="user.id" class="ma-3 pa-3" style="width:300px;" :to="{ name : 'users.read', params : { id : user.id }}">
                <v-card-title  v-if="user.first_name || user.last_name">
                    <div class="headline mb-0">
                        {{ user.first_name }} {{ user.last_name }}
                    </div>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-media :src="noAvatarImage" class="mt-2 mb-2" height="300px">
                </v-card-media>
                <v-divider></v-divider>
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex xs2 class="pr-1">
                            <v-icon>fa-envelope</v-icon>
                        </v-flex>
                        <v-flex xs10>
                            {{ user.email }}
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        computed : {
            noAvatarImage() {
                return require('@/apps/users/images/no_avatar.png');
            },
            users() {
                return this.$store.getters['userModule/users'];
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
