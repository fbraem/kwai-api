<?php
require __DIR__ . '/../vendor/autoload.php';

use Kwai\Core\Infrastructure\Database\DatabaseException;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Context;

$db = Context::withDatabase();
if ($db) {
    // Migrate the database, if needed.
    // phinx needs a database fetch mode FETCH_ASSOC
    $db->asArray();

    $configArray = require(__DIR__ . '/../phinx.php');
    $configArray['environments']['test'] = [
        'connection' => $db->getPDO()
    ];
    $manager = new Manager(
        new Config($configArray),
        new StringInput(' '),
        new NullOutput()
    );
    $manager->migrate('test');

    // Reset the fetch mode for our tests
    $db->asObject();
}
