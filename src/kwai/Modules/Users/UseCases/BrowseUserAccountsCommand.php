<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class BrowseUserAccountsCommand
 *
 * Command for the BrowseUserAccounts use case.
 * @see BrowseUserAccounts
 */
class BrowseUserAccountsCommand
{
    public ?int $limit = null;

    public ?int $offset = null;
}
