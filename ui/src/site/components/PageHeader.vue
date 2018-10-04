<template>
    <section class="uk-section uk-section-small uk-section-secondary">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div v-if="picture" class="uk-width-1-1 uk-width-1-2@m uk-width-2-5@l">
                    <img :src="picture" />
                </div>
                <div class="uk-width-1-1" :class="{ 'uk-width-1-2@m' : picture != null, 'uk-width-3-5@l' : picture != null }">
                    <slot />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props : [ 'picture' ]
    };
</script>
