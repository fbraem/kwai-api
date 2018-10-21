<template>
    <div>
        <div class="uk-card uk-card-hover uk-card-body" uk-scrollspy="cls:uk-animation-slide-top-small">
            <router-link :to="{ name : 'categories.read', params : { id : category.id } }" class="uk-link-reset uk-position-cover uk-position-z-index uk-margin-remove-adjacent" />
            <img v-if="category.icon_picture" :src="category.icon_picture" style="color:#c61c18;" width="48" height="48" uk-svg />
            <h4 class="uk-margin">{{ category.name }}</h4>
            <div class="uk-margin">
                {{ category.short_description }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : {
            category : {
                type : Object,
                required : true
            }
        }
    }
</script>
