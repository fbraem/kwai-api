<template>
    <v-list-tile :key="member.id" @click="">
        <v-list-tile-content>
            <v-list-tile-title>{{ member.person.name }}</v-list-tile-title>
            <v-list-tile-sub-title>{{ member.person.formatted_birthdate }} &ndash; {{ age }} {{ $t('age') }}</v-list-tile-sub-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-list-tile-action-text>{{ member.license }}</v-list-tile-action-text>
            <v-icon class="fas">{{ icon }}</v-icon>
        </v-list-tile-action>
    </v-list-tile>
</template>

<script>
    import messages from '../lang/lang';

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
            age() {
                if (this.team.season) {
                    return this.team.season.end_date.diff(this.member.person.birthdate, 'years');
                }
                return this.member.person.age;
            },
            icon() {
                if (this.member.person.isFemale) return 'fa-female';
                if (this.member.person.isMale) return 'fa-male';
                return 'fa-question';
            }
        }
    };
</script>
