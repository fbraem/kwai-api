<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\Ability;

/**
 * Ability repository interface
 */
interface AbilityRepository
{
    /**
     * Create a new ability entity
     *
     * @param Ability $ability
     * @throws RepositoryException
     * @return Entity<Ability>
     */
    public function create(Ability $ability): Entity;

    /**
     * Get an ability.
     *
     * @param  int $id Id of an ability
     * @throws RepositoryException
     * @throws NotFoundException
     * @return Entity<Ability>  An ability
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById(int $id) : Entity;

    /**
     * Get all abilities of the user
     * @param  Entity<User> $user
     * @throws RepositoryException
     * @return Ability[]
     */
    public function getByUser(Entity $user): array;

    /**
     * Get all abilities
     * @throws RepositoryException
     * @return Ability[]
     */
    public function getAll(): array;
}
