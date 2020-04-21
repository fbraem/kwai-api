<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);


namespace Kwai\Core\Infrastructure\Repositories;

/**
 * Interface ImageRepository
 */
interface ImageRepository
{
    /**
     * Return all images for an entity with the given id.
     *
     * @param int $id
     * @return array
     */
    public function getImages(int $id): array;
}
