<template>
    <section class="uk-section uk-section-default uk-section-small">
        <div class="uk-container uk-container-expand">
            <div class="uk-child-width-1-1 uk-child-width-1-2@l" uk-grid="masonry: true">
                <div v-for="category in categories">
                    <router-link :to="{ name: 'categories.read', params: { id : category.id } }" class="uk-link-reset">
                        <div class="uk-card uk-card-body uk-card-default uk-card-small uk-card-hover" style="border:1px solid rgba(0,0,0,0.075);">
                            <h3 class="uk-card-title">
                                {{ category.name }}
                            </h3>
                            <p v-html="category.description">
                            </p>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        computed : {
            categories() {
                return this.$store.getters['categoryModule/categories'];
            }
        },
        created() {
            this.$store.dispatch('categoryModule/browse');
        },
        beforeRouteUpdate(to, from, next) {
        	this.$store.dispatch('categoryModule/browse');
        	next();
        },
    };
</script>
