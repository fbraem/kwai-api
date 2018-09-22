<template>
    <Page>
        <template slot="title">
            {{ $t('teams') }} &bull;&nbsp;
            <span v-if="creating">{{ $t('team.create') }}</span>
            <span v-else>{{ $t('team.update') }}</span>
        </template>
        <section slot="content" class="uk-section uk-section-default uk-section-small">
            <div class="uk-container">
                <div uk-grid>
                    <div class="uk-width-1-1">
                        <form class="uk-form-stacked">
                            <uikit-input-text v-model="form.team.name" :validator="$v.form.team.name" :errors="nameErrors" id="name" :placeholder="$t('team.form.name.placeholder')">
                                {{ $t('team.form.name.label') }}:
                            </uikit-input-text>
                            <uikit-select
                                v-model="form.team.season"
                                :items="seasons"
                                :validator="$v.form.team.season"
                                :errors="seasonErrors"
                                id="season"
                                :empty="$t('team.form.season.placeholder')">
                                {{ $t('team.form.season.label') }}:
                            </uikit-select>
                            <p class="uk-text-meta">{{ $t('team.form.season.hint')}}</p>
                            <uikit-select
                                v-model="form.team.team_type"
                                :items="team_types"
                                :validator="$v.form.team.team_type"
                                :errors="team_typeErrors"
                                id="team_type"
                                :empty="$t('team.form.team_type.placeholder')">
                                {{ $t('team.form.team_type.label') }}:
                            </uikit-select>
                            <p class="uk-text-meta">{{ $t('team.form.team_type.hint')}}</p>
                            <uikit-textarea v-model="form.team.remark" :validator="$v.form.team.remark" :rows="5" id="remark" :errors="remarkErrors" :placeholder="$t('team.form.remark.placeholder')">
                                {{ $t('team.form.remark.label') }}:
                            </uikit-textarea>
                        </form>
                    </div>
                    <div uk-grid class="uk-width-1-1">
                        <div class="uk-width-expand">
                        </div>
                        <div class="uk-width-auto">
                            <button class="uk-button uk-button-primary" :disabled="$v.$invalid" @click="submit">
                                <fa-icon name="save" />&nbsp; {{ $t('save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </Page>
</template>

<script>
    import Team from '../models/Team';
    import TeamType from '../models/TeamType';
    import Season from '@/apps/seasons/models/Season';

    import { validationMixin } from 'vuelidate';
    import { required, numeric } from 'vuelidate/lib/validators';

    import 'vue-awesome/icons/save';

    import Page from './Page.vue';
    import UikitInputText from '@/components/uikit/InputText.vue';
    import UikitTextarea from '@/components/uikit/Textarea.vue';
    import UikitSelect from '@/components/uikit/Select.vue';

    var initError = function() {
        return {
            name : [],
            season : [],
            team_type : [],
            remark : []
        }
    };
    var initForm = function() {
        return {
            team : {
                name : '',
                season : 0,
                team_type : 0,
                remark : ''
            }
        };
    }

    import messages from '../lang';
    import teamStore from '@/apps/teams/store';
    import seasonStore from '@/apps/seasons/store';

    export default {
        components : {
            Page, UikitInputText, UikitTextarea, UikitSelect
        },
        i18n : messages,
        mixins: [
            validationMixin
        ],
        data() {
            return {
                team : new Team(),
                form : initForm(),
                errors : initError()
            }
        },
        computed : {
            creating() {
                return this.team != null && this.team.id == null;
            },
            error() {
                return this.$store.getters['teamModule/error'];
            },
            seasons() {
                var seasons = this.$store.getters['seasonModule/seasons'].map((season) => ({value : season.id, text : season.name }));
                seasons.unshift({ value : 0, text : '< ' + this.$t('no_season') + ' >'});
                return seasons;
            },
            team_types() {
                var types = this.$store.getters['teamModule/types'].map((type) => ({value : type.id, text : type.name }));
                types.unshift({ value : 0, text : '< ' + this.$t('no_type') + ' >'});
                return types;
            },
            nameErrors() {
                const errors = [...this.errors.name];
                if (! this.$v.form.team.name.$dirty) return errors;
                ! this.$v.form.team.name.required && errors.push(this.$t('required'));
                return errors;
            },
            seasonErrors() {
                const errors = [...this.errors.season];
                if (! this.$v.form.team.season.$dirty) return errors;
                return errors;
            },
            team_typeErrors() {
                const errors = [...this.errors.team_type];
                if (! this.$v.form.team.team_type.$dirty) return errors;
                return errors;
            },
            remarkErrors() {
                const errors = [...this.errors.remark];
                if (! this.$v.form.team.remark.$dirty) return errors;
                return errors;
            }
        },
        validations : {
            form : {
                team : {
                    name : { required },
                    season : {},
                    team_type : {},
                    remark : {}
                }
            }
        },
        beforeCreate() {
            if (!this.$store.state.teamModule) {
                this.$store.registerModule('teamModule', teamStore);
            }
            if (!this.$store.state.seasonModule) {
                this.$store.registerModule('seasonModule', seasonStore);
            }
        },
        async created() {
            await this.$store.dispatch('seasonModule/browse');
            await this.$store.dispatch('teamModule/browseType')
        },
        beforeRouteEnter(to, from, next) {
            next(async (vm) => {
                if (to.params.id) await vm.fetchData(to.params.id);
                next();
            });
        },
        watch : {
            team(nv) {
                if (nv) {
                    this.fillForm(nv);
                }
            },
            error(nv) {
                if (nv) {
                    if ( nv.response.status == 422 ) {
                        nv.response.data.errors.forEach((item, index) => {
                            if ( item.source && item.source.pointer ) {
                                var attr = item.source.pointer.split('/').pop();
                                this.errors[attr].push(item.title);
                            }
                        });
                    }
                    else if ( nv.response.status == 404 ){
                      //this.error = err.response.statusText;
                    }
                    else {
                      //TODO: check if we can get here ...
                      console.log(nv);
                    }
                }
            }
        },
        methods : {
            async fetchData(id) {
                this.team = await this.$store.dispatch('teamModule/read', {
                    id : id
                });
                this.fillForm();
            },
            clear() {
                this.$v.$reset();
                this.form = initForm();
            },
            fillForm() {
                this.form.team.name = this.team.name;
                this.form.team.remark = this.team.remark;
                if (this.team.season) {
                    this.form.team.season = this.team.season.id;
                }
                if (this.team.team_type) {
                    this.form.team.team_type = this.team.team_type.id;
                }
            },
            fillTeam() {
                this.team.name = this.form.team.name;
                this.team.remark = this.form.team.remark;
                if (this.form.team.season) {
                    this.team.season = new Season();
                    this.team.season.id = this.form.team.season;
                }
                if (this.form.team.team_type) {
                    this.team.team_type = new TeamType();
                    this.team.team_type.id = this.form.team.team_type;
                }
            },
            submit() {
                this.errors = initError();
                this.fillTeam();
                this.$store.dispatch('teamModule/save', this.team)
                    .then((newTeam) => {
                        this.$router.push({ name : 'teams.read', params : { id : newTeam.id }});
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
        }
    };
</script>
