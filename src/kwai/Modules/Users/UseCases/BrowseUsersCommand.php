<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class BrowseUsersCommand
 *
 * Command for the BrowseUsers use case.
 */
class BrowseUsersCommand
{
    public ?int $limit = null;

    public ?int $offset = null;
}
