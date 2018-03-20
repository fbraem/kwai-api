<template>
    <v-list-tile avatar :to="{ name : 'season.read', params : { 'id' : season.id } }">
        <v-list-tile-avatar>
            <v-icon v-if="active">fa-check</v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>{{ season.name }}</v-list-tile-title>
            <v-list-tile-sub-title>{{ start }} &ndash; {{ end }} <span v-if="season.remark">&ndash; {{ season.remark }}</span></v-list-tile-sub-title>
        </v-list-tile-content>
    </v-list-tile>
</template>

<script>
    import moment from 'moment';

    export default {
        props : {
            season : {
                type : Object,
                required : true
            }
        },
        computed : {
            start() {
                var date = moment(this.season.start_date, 'YYYY-MM-DD');
                return date.format('L');
            },
            end() {
                var date = moment(this.season.end_date, 'YYYY-MM-DD');
                return date.format('L');
            },
            active() {
                var today = moment();
                var start = moment(this.season.start_date, 'YYYY-MM-DD');
                var end = moment(this.season.end_date, 'YYYY-MM-DD');
                return today.isBetween(start, end) || today.isSame(start) || today.isSame(end);
            }
        }
    };
</script>
