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
 * Class FileSystemDepedency
 */
class FileSystemDepedency
{
    public function __invoke(array $settings)
    {
        $flyAdapter = new Local($settings['files']);
        return new Filesystem($flyAdapter);
    }
}
