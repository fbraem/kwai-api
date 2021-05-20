<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

/**
 * Class UpdateApplicationCommand
 */
class UpdateApplicationCommand
{
    /**
     * The id of the application to update.
     */
    public int $id;

    /**
     * The title of the application.
     */
    public string $title;

    /**
     * A short description of the application.
     */
    public string $short_description;

    /**
     * A description of the application.
     */
    public string $description;
}
