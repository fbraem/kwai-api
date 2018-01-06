<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base User Migration
 */
class UserMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users', ['signed' => false])
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('last_login', 'datetime', ['null' => true])
            ->addColumn('first_name', 'string', ['null' => true])
            ->addColumn('last_name', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('user_logs', ['signed' => false])
            ->addColumn('user_id', 'integer')
            ->addColumn('action', 'string')
            ->addColumn('domain', 'string')
            ->addColumn('model_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $pwd = $this->randomPassword();

        $application = \Core\Clubman::getApplication();
        $db = $application->getContainer()['db'];
        //TODO: Use User class
        $table = new \Zend\Db\TableGateway\TableGateway('users', $db);
        $table->insert([
            'email' => $application->getConfig()->email,
            'password' => password_hash($pwd, PASSWORD_DEFAULT),
            'remark' => 'Clubman Root User',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);

        echo 'Root User', PHP_EOL;
        echo '---------', PHP_EOL;
        echo 'Email: ', $application->getConfig()->email, PHP_EOL;
        echo 'Password: ', $pwd, PHP_EOL;
    }

    public function down()
    {
        $this->table('users')->drop();
        $this->table('user_logs')->drop();
    }

    private function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
