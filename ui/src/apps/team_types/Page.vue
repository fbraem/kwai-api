<template>
    <div>
        <header>
            <div class="uk-navbar-container" uk-navbar>
                <div class="uk-navbar-left">
                    <span class="uk-navbar-item uk-logo"><slot name="title"></slot></span>
                </div>
                <div class="uk-navbar-right uk-padding">
                    <router-link v-if="$team.isAllowed('create')" class="uk-icon-button" :to="{ 'name' : 'team_types.create' }">
                        <i class="fas fa-plus"></i>
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
    export default {
    };
</script>
