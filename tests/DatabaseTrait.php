<?php
declare(strict_types=1);


namespace Tests;


use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Modules\Users\Domain\User;
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

    public function withUser(): Entity
    {
        return new Entity(
            1,
            new User(
                new UniqueId(),
                new EmailAddress('jigoro.kano@kwai.com'),
                new Name('Jigoro', 'Kano')
            )
        );
    }
}
