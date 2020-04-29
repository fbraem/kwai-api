<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);


namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface StoryFilter
 *
 * Interface for querying news stories.
 */
interface StoryQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     */
    public function filterId(int $id): void;

    /**
     * Add a filter on the publication date.
     * When month is omitted, all stories of the year will be returned.
     *
     * @param int      $year
     * @param int|null $month
     */
    public function filterPublishDate(int $year, ?int $month): void;

    /**
     * Only select the promoted news stories.
     */
    public function filterPromoted(): void;
}
