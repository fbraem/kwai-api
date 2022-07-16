<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class UpdateDefinitionCommand
 *
 * Command for the use case Update Definition
 */
final class UpdateDefinitionCommand
{
    /**
     * The id of the definition to update
     */
    public int $id;

    use DefinitionTrait;
}
