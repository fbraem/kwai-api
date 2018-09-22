<template>
    <div>
        <header>
            <div class="uk-navbar-container" uk-navbar>
                <div class="uk-navbar-left">
                    <span class="uk-navbar-item uk-logo"><slot name="title"></slot></span>
                </div>
                <div class="uk-navbar-right uk-padding">
                    <router-link v-if="$team.isAllowed('create')" class="uk-icon-button" :to="{ 'name' : 'teams.create' }">
                        <fa-icon name="plus" />
                    </router-link>
                    <slot name="toolbar"></slot>
                </div>
            </div>
        </header>
        <section v-if="$slots.header" class="uk-section uk-section-secondary">
            <slot name="header"></slot>
        </section>
        <section class="uk-section uk-section-default uk-padding-small">
            <slot name="content"></slot>
        </section>
    </div>
</template>

<script>
    import 'vue-awesome/icons/plus';
    export default {
    };
</script>
