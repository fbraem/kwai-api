<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types=1);

namespace Kwai\Applications\Auth\Resources;

/**
 * Enum ResourceTypes
 *
 * All types for JSONAPI resources.
 */
enum ResourceTypes: string
{
    const USER_RECOVERIES = 'user_recoveries';
}
