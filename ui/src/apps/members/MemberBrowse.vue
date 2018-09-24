<template>
    <Page>
        <template slot="title">{{ $t('members') }}</template>
        <div slot="content" class="uk-container">
            <div v-if="$wait.is('members.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <fa-icon name="spinner" scale="2" spin />
                </div>
            </div>
            <div class="uk-child-width-1-1" uk-grid>
                <div class="uk-flex uk-flex-center uk-grid-small" uk-grid>
                    <div v-for="(group, letter) in members" :key="letter">
                        <span class="uk-label" >
                            <a class="uk-link-reset" @click="jumpIt('#letter-' + letter)">{{letter}}</a>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="uk-column-1-2@s uk-column-1-3@m">
                        <div v-for="(group, letter) in members" :key="letter">
                            <h3 class="uk-heading-bullet" :id="'letter-' + letter">{{ letter }}</h3>
                            <ul class="uk-list">
                                <li v-for="member in group" :key="member.id">
                                    <span class="uk-text-meta">{{ member.license }}</span> - {{ member.person.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Page>
</template>

<script>
    import memberStore from '@/stores/members';

    import messages from './lang';
    import jump from 'jump.js';

    import Page from './Page.vue';

    export default {
        components : {
            Page
        },
        i18n : messages,
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
        beforeCreate() {
            if (!this.$store.state.memberModule) {
                this.$store.registerModule('memberModule', memberStore);
            }
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                await vm.fetchData();
                next();
            });
        },
        methods : {
            fetchData() {
                this.$store.dispatch('memberModule/browse', {});
            },
            jumpIt(target) {
                jump(target);
            }
        }
    };
</script>
