<template>
    <v-container fluid>
        <v-card>
            <v-card-text v-if="seasons">
                <v-list v-if="seasons.length > 0" two-line>
                    <SeasonListItem v-for="season in seasons" :key="season.id" :season="season" />
                </v-list>
                <div v-else>
                    {{ $t('no_seasons') }}
                </div>
            </v-card-text>
        </v-card>
        <v-btn v-if="$isAllowed('create')" icon :to="{ name : 'season.create' }" fab small>
            <v-icon>fa-plus</v-icon>
        </v-btn>
    </v-container>
</template>

<script>
    import messages from '../lang/lang';

    import SeasonListItem from './SeasonListItem';

    export default {
        components : {
            SeasonListItem
        },
        i18n : {
            messages
        },
        data() {
            return {
            };
        },
        mounted() {
            this.$store.dispatch('seasonModule/browse');
        },
        computed : {
            seasons() {
                return this.$store.getters['seasonModule/seasons'];
            }
        }
    };
</script>
