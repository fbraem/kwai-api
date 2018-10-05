<template>
    <section class="uk-section uk-section-small uk-section-secondary">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div v-if="picture" class="uk-width-1-1 uk-width-1-2@m uk-width-2-3@l uk-width-3-5@xl uk-flex uk-flex-middle">
                    <div>
                        <img :src="picture" />
                    </div>
                </div>
                <div class="uk-width-1-1" :class="{ 'uk-width-1-2@m' : picture != null, 'uk-width-1-3@l' : picture != null, 'uk-width-2-5@xl' : picture != null }">
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
