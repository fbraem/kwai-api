<template>
    <div class="uk-card uk-card-default">
        <div class="uk-card-header">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-auto">
                    <img class="uk-border-circle" width="40" height="40" :src="noAvatarImage">
                </div>
                <div class="uk-width-expand">
                    <h3 class="uk-card-title uk-margin-remove-bottom">{{ user.name }}</h3>
                    <p class="uk-text-meta uk-margin-remove-top"><i class="fas fa-envelope"></i>&nbsp;<a :href="'mailto:' + user.email">{{ user.email }}</a></p>
                </div>
            </div>
        </div>
        <div class="uk-card-body">
        </div>
        <div class="uk-card-footer">
            <strong>{{ $t('last_login') }} :</strong> {{ user.lastLoginFormatted }}
            <span class="uk-float-right">
                <router-link class="uk-icon-button" :to="userLink">
                    <i class="fas fa-ellipsis-h"></i>
                </router-link>
            </span>
        </div>
    </div>
</template>

<script>
    import messages from '../lang';

    export default {
        i18n : messages,
        props : [
            'user'
        ],
        computed : {
            noAvatarImage() {
                return require('@/apps/users/images/no_avatar.png');
            },
            userLink() {
                return {
                    name : 'users.read',
                    params : {
                        id : this.user.id
                    }
                };
            }
        }
    };
</script>
