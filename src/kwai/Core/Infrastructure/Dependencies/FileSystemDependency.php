<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use League\Flysystem\Filesystem;

/**
 * Class FileSystemDependency
 */
class FileSystemDependency implements Dependency
{
    public function __construct(
        private ?Configuration $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create(): Filesystem
    {
        return $this->settings->getFilesystemConfiguration()->createFilesystem();
    }
}
