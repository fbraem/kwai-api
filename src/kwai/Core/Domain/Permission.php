<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain;

/**
 * Enum Permission
 */
enum Permission: int
{
    case CanView = 1;
    case CanCreate = 2;
    case CanUpdate = 4;
    case CanDelete = 8;
}
