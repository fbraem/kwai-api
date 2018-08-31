<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/user_invitations', function () {
    $this->post('', \REST\Users\Actions\CreateInvitationAction::class)
        ->setName('user_invitations.create')
        ->setArgument('auth', true)
    ;
});

$app->run();
