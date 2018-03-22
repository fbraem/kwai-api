<template>
    <v-list-tile avatar :to="{ name : 'team.read', params : { 'id' : team.id } }">
        <v-list-tile-avatar>
            <v-icon v-if="team.active">fa-check</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>{{ team.name }}</v-list-tile-title>
            <v-list-tile-sub-title v-if="team.season">Season {{ team.season.name }}</v-list-tile-sub-title>
        </v-list-tile-content>
    </v-list-tile>
</template>

<script>
    export default {
        props : {
            team : {
                type : Object,
                required : true
            }
        }
    };
</script>
