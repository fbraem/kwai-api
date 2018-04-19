<template>
    <v-list-tile :key="member.id" @click="">
        <v-list-tile-content>
            <v-list-tile-title>{{ name }}</v-list-tile-title>
            <v-list-tile-sub-title>{{ birthdate }} &ndash; {{ age }} {{ $t('age') }}</v-list-tile-sub-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-list-tile-action-text>{{ member.license }}</v-list-tile-action-text>
            <v-icon class="fas">{{ icon }}</v-icon>
        </v-list-tile-action>
    </v-list-tile>
</template>

<script>
    import messages from '../lang/lang';
    import moment from 'moment';

    export default {
        i18n : {
            messages
        },
        props : {
            team : {
                type : Object,
                required : true
            },
            member : {
                type : Object,
                required : true
            }
        },
        computed : {
            name() {
                return this.member.person.lastname + ' ' + this.member.person.firstname;
            },
            birthdate() {
                return moment(this.member.person.birthdate, 'YYYY-MM-DD').format('L');
            },
            age() {
                if (this.team.season) {
                    return moment(this.team.season.end_date, 'YYYY-MM-DD').diff(moment(this.member.person.birthdate, 'YYYY-MM-DD'), 'years');
                }
                return moment().diff(moment(this.member.person.birthdate, 'YYYY-MM-DD'), 'years');
            },
            icon() {
                if (this.member.person.gender == 2) return 'fa-female';
                if (this.member.person.gender == 1) return 'fa-male';
                return 'fa-question';
            }
        }
    };
</script>
