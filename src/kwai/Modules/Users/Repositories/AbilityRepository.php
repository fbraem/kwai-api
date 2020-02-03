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
     * @return Entity<Ability>  An ability
     */
    public function getById(int $id) : Entity;

    /**
     * Get all abilities of the user
     * @param  Entity<User> $user
     * @return array
     */
    public function getByUser(Entity $user): array;
}
