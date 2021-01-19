<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

use Kwai\Core\UseCases\Content;

/**
 * Class UpdatePageCommand
 *
 * Command class for the UpdatePage use case.
 */
class UpdatePageCommand
{
    public int $id;

    public bool $enabled = false;

    public int $application;

    public int $priority = 0;

    public ?string $remark = null;

    /**
     * @var Content[]
     */
    public array $contents = [];
}
