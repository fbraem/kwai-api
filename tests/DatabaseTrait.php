<?php
declare(strict_types=1);

namespace Tests;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

trait DatabaseTrait
{
    protected ?Connection $db = null;

    public function hasDatabase(): bool
    {
        return $this->db !== null;
    }

    public function isDatabaseDriver(string $driver): bool
    {
        if ($this->db === null) {
            return false;
        }
        return $this->db->getDriver() === $driver;
    }

    public function withDatabase(): self
    {
        static $migrated = false;

        $this->db = depends('kwai.db', DatabaseDependency::class);

        if (!$migrated) {
            // Migrate the database, if needed.
            $configArray = require(__DIR__ . '/../src/phinx.php');
            $configArray['environments']['test']['connection'] = $this->db->getPDO();
            $configArray['environments']['test']['name'] = 'kwai';
            $manager = new Manager(
                new Config($configArray),
                new StringInput(' '),
                new NullOutput()
            );
            $manager->migrate('test');

            $migrated = true;
        }

        return $this;
    }

    public function withUser(): UserEntity
    {
        $this->withDatabase();
        $repo = new UserDatabaseRepository($this->db);
        $user = $repo->getById(1);
        return new UserEntity(
            $user->id(),
            new User(
                uuid: $user->getUuid(),
                emailAddress: $user->getEmailAddress(),
                username: $user->getUsername(),
                admin: true
            )
        );
    }
}
