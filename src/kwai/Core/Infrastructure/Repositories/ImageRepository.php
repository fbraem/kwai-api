<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);


namespace Kwai\Core\Infrastructure\Repositories;

use Illuminate\Support\Collection;

/**
 * Interface ImageRepository
 */
interface ImageRepository
{
    /**
     * Return all images for an entity with the given id.
     *
     * @param int $id
     * @return Collection
     */
    public function getImages(int $id): Collection;

    /**
     * Remove all images for the entity with the given id
     *
     * @param int $id
     */
    public function removeImages(int $id): void;
}
