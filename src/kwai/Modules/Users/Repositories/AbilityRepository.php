<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;

/**
 * Ability repository interface
 */
interface AbilityRepository
{
    /**
     * Get an ability.
     *
     * @param  int $id Id of an ability
     * @return Entity  An ability
     */
    public function getById(int $id) : Entity;
}
