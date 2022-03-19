<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * Class FilesystemConfiguration
 */
class FilesystemConfiguration implements Configurable
{
    public function __construct(
        private string $local,
        private string $url
    ) {
    }

    /**
     * Returns the base url for files.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    public function createFilesystem(): Filesystem
    {
        return new Filesystem(new Local($this->local));
    }

    public static function createFromVariables(array $variables): self
    {
        return new self(
            $variables['KWAI_FS_LOCAL'],
            $variables['KWAI_FS_URL']
        );
    }

    public static function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_FS_LOCAL',
            'KWAI_FS_URL'
        ]);
    }
}
