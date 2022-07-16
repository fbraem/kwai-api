<?php
/**
 * @package Module
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class BrowseDefinitionsCommand
 */
final class BrowseDefinitionsCommand
{
    public ?int $limit = null;
    public ?int $offset = null;
}
