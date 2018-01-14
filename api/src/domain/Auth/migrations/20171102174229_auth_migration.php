<?php

use Phinx\Migration\AbstractMigration;

class AuthMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('oauth_scopes', ['signed' => false])
            ->addColumn('identifier', 'string', ['limit' => 128])
            ->addColumn('description', 'string', ['null' => true, 'limit' => 255])
            ->addTimestamps()
            ->create()
        ;

        $this->table('oauth_grants', ['signed' => false])
            ->addColumn('identifier', 'string', ['limit' => 128])
            ->addColumn('description', 'string', ['null' => true, 'limit' => 255])
            ->addTimestamps()
            ->create()
        ;

        $this->table('oauth_clients', ['signed' => false])
            ->addColumn('name', 'string', ['limit' => 128])
            ->addColumn('identifier', 'string', ['limit' => 128])
            ->addColumn('secret', 'string', ['limit' => 128])
            ->addColumn('redirect_uri', 'string', ['limit' => 2000])
            ->addColumn('status', 'boolean', ['null' => true, 'default' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('oauth_client_scopes', ['id' => false, 'primary_key' => ['client_id', 'scope_id']])
            ->addColumn('client_id', 'integer')
            ->addColumn('scope_id', 'integer')
            ->create()
        ;

        $this->table('oauth_client_grants', ['id' => false, 'primary_key' => ['client_id', 'grant_id']])
            ->addColumn('client_id', 'integer')
            ->addColumn('grant_id', 'integer')
            ->create()
        ;

        $this->table('oauth_user_grants', ['id' => false, 'primary_key' => ['user_id', 'grant_id']])
            ->addColumn('user_id', 'integer')
            ->addColumn('grant_id', 'integer')
            ->create()
        ;

        $this->table('oauth_user_scopes', ['id' => false, 'primary_key' => ['user_id', 'scope_id']])
            ->addColumn('user_id', 'integer')
            ->addColumn('scope_id', 'integer')
            ->create()
        ;

        $this->table('oauth_access_tokens', ['signed' => false])
            ->addColumn('identifier', 'string', ['limit' => 128])
            ->addColumn('client_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('expiration', 'timestamp', ['null' => true])
            ->addColumn('revoked', 'boolean', [ 'default' => false])
            ->addColumn('type', 'integer', ['signed' => false, 'default' => 1])
            ->addTimestamps()
            ->create()
        ;

        $this->table('oauth_access_token_scopes', ['id' => false, 'primary_key' => ['access_token_id', 'scope_id']])
            ->addColumn('access_token_id', 'integer')
            ->addColumn('scope_id', 'integer')
            ->create()
        ;

        $this->table('oauth_refresh_tokens', ['signed' => false])
            ->addColumn('identifier', 'string', ['limit' => 128])
            ->addColumn('access_token_id', 'integer')
            ->addColumn('expiration', 'timestamp', ['null' => true])
            ->addColumn('revoked', 'boolean', ['default' => false])
            ->addTimestamps()
            ->create()
        ;

        $this->table('oauth_auth_codes', ['signed' => false])
            ->addColumn('identifier', 'string', ['limit' => 128])
            ->addColumn('user_id', 'integer')
            ->addColumn('client_id', 'integer')
            ->addColumn('expiration', 'timestamp', ['null' => true])
            ->addColumn('redirect_uri', 'string', ['limit' => 2000])
            ->addColumn('revoked', 'boolean', ['default' => false])
            ->addTimestamps()
            ->create()
        ;

        $this->table('oauth_auth_code_scopes', ['id' => false, 'primary_key' => ['auth_code_id', 'scope_id']])
            ->addColumn('auth_code_id', 'integer')
            ->addColumn('scope_id', 'integer')
            ->create()
        ;

        $application = \Core\Clubman::getApplication();
        $data = [
            [
                'name' => $application->getConfig()->oauth2->client->name,
                'identifier' => $application->getConfig()->oauth2->client->identifier,
                'secret' => password_hash($application->getConfig()->oauth2->client->secret, PASSWORD_BCRYPT),
                'redirect_uri' => $application->getConfig()->oauth2->client->redirect
            ]
        ];
        $this->table('oauth_clients')->insert($data)->save();

        $data = [
            [
                'identifier' => 'basic',
                'description' => 'Basic Scope'
            ]
        ];
        $this->table('oauth_scopes')->insert($data)->save();
    }

    public function down()
    {
        $this->table('oauth_auth_code_scopes')->drop();
        $this->table('oauth_auth_codes')->drop();
        $this->table('oauth_refresh_tokens')->drop();
        $this->table('oauth_access_token_scopes')->drop();
        $this->table('oauth_access_tokens')->drop();
        $this->table('oauth_user_scopes')->drop();
        $this->table('oauth_user_grants')->drop();
        $this->table('oauth_client_grants')->drop();
        $this->table('oauth_client_scopes')->drop();
        $this->table('oauth_clients')->drop();
        $this->table('oauth_grants')->drop();
        $this->table('oauth_scopes')->drop();
    }
}
