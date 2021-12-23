<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * Class FileSystemDependency
 */
class FileSystemDependency implements Dependency
{
    public function __construct(
        private ?array $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create()
    {
        $flyAdapter = new Local($this->settings['files']['local']);
        return new Filesystem($flyAdapter);
    }
}
