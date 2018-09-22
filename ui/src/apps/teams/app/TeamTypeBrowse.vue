<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                <div>
                    <h3 class="headline mb-0">{{ $t('types') }}</h3>
                </div>
            </v-card-title>
            <v-card-text v-if="this.types">
                <v-list v-if="this.types.length > 0" two-line>
                    <TeamTypeListItem v-for="type in types" :key="type.id" :type="type" />
                </v-list>
                <div v-else>
                    {{ $t('no_types') }}.
                </div>
            </v-card-text>
        </v-card>
        <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'teamtype.create' }" fab small>
            <v-icon>fa-plus</v-icon>
        </v-btn>
    </v-container>
</template>

<script>
    import messages from '../lang';

    import TeamTypeListItem from './TeamTypeListItem';

    export default {
        components : {
            TeamTypeListItem
        },
        i18n : {
            messages
        },
        data() {
            return {
            };
        },
        mounted() {
            this.$store.dispatch('teamModule/browseType');
        },
        computed : {
            types() {
                return this.$store.getters['teamModule/types'];
            }
        }
    };
</script>
