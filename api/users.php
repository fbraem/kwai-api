<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/users', function () {
    $this->get('', \REST\Users\Actions\BrowseAction::class)
        ->setName('users.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Users\Actions\ReadAction::class)
        ->setName('users.read')
        ->setArgument('auth', true)
    ;
    $this->post('/{token:[0-9a-zA-Z]+}', \REST\Users\Actions\CreateWithTokenAction::class)
        ->setName('users.create.token')
    ;

    // Rules
    $this->get('/{id:[0-9]+}/rules', \REST\Users\Actions\UserRuleBrowseAction::class)
        ->setName('users.rules.browse')
        ->setArgument('auth', true)
    ;

    $this->get('/rule_groups', \REST\Users\Actions\RuleGroupBrowseAction::class)
        ->setName('users.rule_groups.browse')
        ->setArgument('auth', true)
    ;
    $this->post('/rule_groups', \REST\Users\Actions\RuleGroupCreateAction::class)
        ->setName('users.rule_groups.create')
        ->setArgument('auth', true)
    ;
    $this->get('/rule_groups/{id:[0-9]+}', \REST\Users\Actions\RuleGroupReadAction::class)
        ->setName('users.rule_groups.read')
        ->setArgument('auth', true)
    ;
    $this->get('/rules', \REST\Users\Actions\RuleBrowseAction::class)
        ->setName('users.rules.browse')
        ->setArgument('auth', true)
    ;
});

$app->run();
