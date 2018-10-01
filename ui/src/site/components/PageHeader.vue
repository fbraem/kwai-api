<template>
    <section class="uk-section uk-section-small uk-section-secondary">
        <div class="uk-container uk-container-expand">
            <div uk-grid>
                <div v-if="hasImage" class="uk-width-1-1 uk-width-2-3@m">
                    <slot name="picture" />
                </div>
                <div class="uk-width-1-1" :class="{ 'uk-width-1-3@m' : hasImage }">
                    <slot />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        computed : {
            hasImage() {
                return this.$slots.picture != null;
            }
        }
    };
</script>
