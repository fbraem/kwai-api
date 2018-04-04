<template>
    <v-list-tile :to="{ name : 'team.read', params : { 'id' : team.id } }">
        <v-list-tile-action v-if="team.active">
            <v-icon>fa-check</v-icon>
        </v-list-tile-action>
        <v-list-tile-content>
            <v-list-tile-title>{{ team.name }}</v-list-tile-title>
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
