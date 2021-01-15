<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

/**
 * Class BrowseUserInvitationsCommand
 *
 * Command for the BrowseUserInvitations use case.
 */
class BrowseUserInvitationsCommand
{
    /**
     * Only show invitations that are still valid until the given time.
     * @var string|null
     */
    public ?string $active_time = null;

    /**
     * The timezone of $active_time
     * @var string|null
     */
    public ?string $active_timezone = null;

    /**
     * Limit of returned records
     *
     * @var int|null
     */
    public ?int $limit = null;

    /**
     * Offset to start returning records
     *
     * @var int|null
     */
    public ?int $offset = null;
}
