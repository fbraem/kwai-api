<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

/**
 * Class Settings
 */
class Settings implements Dependency
{
    public function create()
    {
        return include __DIR__ . '/../../../../../config/config.php';
    }
}
