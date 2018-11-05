<template>
    <div>
        <PageHeader>
            <div class="uk-grid">
                <div class="uk-width-5-6">
                    <h1 class="uk-h1 uk-margin-remove">{{ $t('members') }}</h1>
                    <h3 class="uk-h3 uk-margin-remove">{{ $t('upload') }}</h3>
                </div>
                <div class="uk-width-1-6">
                </div>
            </div>
        </PageHeader>
        <section class="uk-section uk-section-small uk-container uk-container-expand">
            <div class="uk-child-width-1-1" uk-grid>
                <div>
                    <div ref="upload" class="uk-placeholder uk-text-center">
                        <i class="uk-text-middle fas fa-cloud-upload-alt fa-2x uk-margin-right"></i>
                        <span class="uk-text-middle">Attach binaries by dropping them here or</span>
                        <div uk-form-custom>
                            <input type="file" multiple>
                            <span class="uk-link">selecting one</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import OAuth from '@/js/oauth';
    const oauth = new OAuth();

    import memberStore from '@/stores/members';

    import UIkit from 'uikit';

    import messages from './lang';

    import PageHeader from '@/site/components/PageHeader';

    export default {
        components : {
            PageHeader
        },
        i18n : messages,
        data() {
            return {
            }
        },
        mounted() {
            UIkit.upload(this.$refs.upload, {
                url : '/api/sport/judo/members/upload',
                multiple : false,
                name : 'csv',
                beforeSend(env) {
                    env.headers['Authorization'] = oauth.getAuthorizationHeader();
                },
                complete(e) {
                    var data = JSON.parse(e.response).data;
                    console.log(data);
                }
            });
        }
    };
</script>
