<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/user_invitations', function () {
    $this->post('', \REST\Users\Actions\CreateInvitationAction::class)
        ->setName('user_invitations.create')
        ->setArgument('auth', true)
    ;
    $this->get('', \REST\Users\Actions\BrowseInvitationAction::class)
        ->setName('user_invitations.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{token:[0-9a-zA-Z]+}', \REST\Users\Actions\ReadInvitationByTokenAction::class)
        ->setName('user_invitations.token')
    ;
});

$app->run();
