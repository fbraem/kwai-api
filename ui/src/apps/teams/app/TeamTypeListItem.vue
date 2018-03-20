<template>
    <v-list-tile avatar :to="{ name : 'teamtype.read', params : { 'id' : type.id } }">
        <v-list-tile-avatar>
            <v-icon v-if="type.active">fa-check</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>{{ type.name }}</v-list-tile-title>
            <v-list-tile-sub-title>{{ type.start_age }} &ndash; {{ type.end_age }} <span v-if="type.remark">&ndash; {{ type.remark }}</span></v-list-tile-sub-title>
        </v-list-tile-content>
    </v-list-tile>
</template>

<script>
    export default {
        props : {
            type : {
                type : Object,
                required : true
            }
        }
    };
</script>
