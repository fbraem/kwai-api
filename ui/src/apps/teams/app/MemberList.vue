<template>
    <v-list v-if="members.length > 0" two-line>
        <v-list-tile v-for="member in members" :key="member.id" @click="">
            <v-list-tile-action v-if="value">
                <v-checkbox v-model="selectedMembers" :value="member.id" @change="updateValue"></v-checkbox>
            </v-list-tile-action>
            <v-list-tile-content>
                <v-list-tile-title>{{ name(member) }}</v-list-tile-title>
                <v-list-tile-sub-title>{{ birthdate(member) }} &ndash; {{ age(member) }} {{ $t('age') }}</v-list-tile-sub-title>
            </v-list-tile-content>
            <v-list-tile-action>
                <v-list-tile-action-text>{{ member.license }}</v-list-tile-action-text>
                <v-icon class="fas">{{ icon(member) }}</v-icon>
            </v-list-tile-action>
        </v-list-tile>
    </v-list>
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
            members : {
                type : Array,
                required : true
            },
            value : {
            }
        },
        data() {
            return {
                selectedMembers : this.value || []
            }
        },
        methods : {
            name(member) {
                return member.person.lastname + ' ' + member.person.firstname;
            },
            birthdate(member) {
                return moment(member.person.birthdate, 'YYYY-MM-DD').format('L');
            },
            age(member) {
                if (this.team.season) {
                    return moment(this.team.season.end_date, 'YYYY-MM-DD').diff(moment(member.person.birthdate, 'YYYY-MM-DD'), 'years');
                }
                return moment().diff(moment(member.person.birthdate, 'YYYY-MM-DD'), 'years');
            },
            icon(member) {
                if (member.person.gender == 2) return 'fa-female';
                if (member.person.gender == 1) return 'fa-male';
                return 'fa-question';
            },
            updateValue() {
                this.$emit('input', this.selectedMembers);
            }
        }
    };
</script>
