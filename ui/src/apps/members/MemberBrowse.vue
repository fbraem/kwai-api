<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-5-6">
                    <h1>{{ $t('members') }}</h1>
                </div>
                <div class="uk-width-1-6">
                    <div class="uk-flex uk-flex-right">
                        <div class="uk-margin-small-left">
                            <router-link v-if="$member.isAllowed('upload')" class="uk-icon-button uk-link-reset" :to="{ name : 'members.upload' }">
                                <i class="fas fa-file-import"></i>
                            </router-link>
                        </div>
<!--
                        <div class="uk-margin-small-left">
                            <router-link v-if="$member.isAllowed('create')" class="uk-icon-button uk-link-reset" :to="{ name : 'members.create' }">
                                <i class="fas fa-plus"></i>
                            </router-link>
                        </div>
-->
                    </div>
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <div v-if="$wait.is('members.browse')" class="uk-flex-center" uk-grid>
                <div class="uk-text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                </div>
            </div>
            <div v-else-if="members" class="uk-child-width-1-1" uk-grid>
                <div v-if="members.length == 0" class="uk-alert uk-alert-warning">
                    {{ $t('no_members') }}
                </div>
                <div v-else class="uk-flex uk-flex-center uk-grid-small" uk-grid>
                    <div v-for="(group, letter) in sortedMembers" :key="letter">
                        <span class="uk-label" >
                            <a class="uk-link-reset" @click="jumpIt('#letter-' + letter)">{{letter}}</a>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="uk-column-1-2@s uk-column-1-3@m uk-column-1-4@xl">
                        <div v-for="(group, letter) in sortedMembers" :key="letter">
                            <h3 class="uk-heading-bullet" :id="'letter-' + letter">{{ letter }}</h3>
                            <ul class="uk-list">
                                <MemberListItem v-for="member in group" :member="member" :key="member.id"></MemberListItem>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import 'flag-icon-css/css/flag-icon.css';

    import memberStore from '@/stores/members';

    import messages from './lang';
    import jump from 'jump.js';

    import PageHeader from '@/site/components/PageHeader';
    import MemberListItem from './MemberListItem.vue';

    export default {
        components : {
            PageHeader, MemberListItem
        },
        i18n : messages,
        data() {
            return {
                count : 0
            };
        },
        computed : {
            members() {
                return this.$store.getters['memberModule/members'];
            },
            sortedMembers() {
                var result = {};
                if (this.members) {
                    this.count = this.members.length;
                    this.members.forEach((e) => {
                        var firstChar = e.person.lastname.charAt(0).toUpperCase();
                        if (!result[firstChar]) result[firstChar] = [];
                        result[firstChar].push(e);
                    });
                } else {
                    this.count = 0;
                }
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
