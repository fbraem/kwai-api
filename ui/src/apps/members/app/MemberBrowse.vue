<template>
    <v-container fluid grid-list-lg>
        <v-layout row wrap>
            <v-flex xs12>
                <v-toolbar class="elevation-0">
                    <v-toolbar-title>{{ $t('members') }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12>
                {{ count }}
            </v-flex>
        </v-layout>
        <v-layout row wrap justify-center>
            <span v-for="(group, letter) in members" :key="letter">
                <v-btn fab small  @click="jumpIt('#letter-' + letter)">{{ letter }}</v-btn>
            </span>
        </v-layout>
        <v-container class="mb-5">
            <v-layout row>
                <v-flex xs12>
                    <div style="column-count:3;clear:both">
                        <v-list two-line subheader>
                            <div v-for="(group, letter) in members" :key="letter">
                                <v-subheader :id="'letter-' + letter">{{letter}}</v-subheader>
                                <v-list-tile avatar v-for="member in group" :key="member.id" @click="" style="break-inside:avoid;">
                                    <v-list-tile-action>
                                        <span v-if="member.person.nationality">{{ member.person.nationality.iso_2 }}</span>
                                    </v-list-tile-action>
                                    <v-list-tile-content>
                                        <v-list-tile-title v-text="member.person.lastname + ' ' + member.person.firstname"></v-list-tile-title>
                                        <v-list-tile-sub-title>{{member.license}}</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                    <v-list-tile-avatar>
                                    </v-list-tile-avatar>
                                </v-list-tile>
                            </div>
                        </v-list>
                    </div>
                </v-flex>
            </v-layout>
        </v-container>
        <v-layout row wrap justify-center>
            <span v-for="(group, letter) in members" :key="letter">
                <v-btn fab small  @click="jumpIt('#letter-' + letter)">{{ letter }}</v-btn>
            </span>
        </v-layout>
    </v-container>
</template>

<script>
    import messages from '../lang/lang';
    import jump from 'jump.js';

    export default {
        i18n : {
            messages
        },
        data() {
            return {
                count : 0
            };
        },
        computed : {
            members() {
                var result = {};
                var members = this.$store.getters['memberModule/members'];
                this.count = members.length;
                members.forEach((e) => {
                    var firstChar = e.person.lastname.charAt(0).toUpperCase();
                    if (!result[firstChar]) result[firstChar] = [];
                    result[firstChar].push(e);
                });
                return result;
            }
        },
        mounted() {
            this.$store.dispatch('memberModule/browse', {});
        },
        methods : {
            jumpIt(target) {
                console.log(target);
                jump(target);
            }
        }
    };
</script>
