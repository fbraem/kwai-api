<template>
    <v-container>
        <v-layout>
            <v-flex xs12>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-toolbar class="elevation-0">
                            <v-icon>add</v-icon>
                            <v-toolbar-title>Create News</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon dark color="primary" @click="$router.go(-1)"><v-icon>cancel</v-icon></v-btn>
                        </v-toolbar>
                    </v-flex>
                    <v-flex xs12>
                        <NewsForm :story="null" />
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import NewsForm from './NewsForm.vue';
    export default {
        components : {
            NewsForm
        }
    };
</script>
