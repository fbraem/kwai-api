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

    /**
     * Add a filter on the application.
     *
     * @param int $id
     */
    public function filterApplication(int $id): void;

    /**
     * Only select news stories that are enabled
     * and don't show news stories which are expired.
     */
    public function filterVisible(): void;

    /**
     * Filter on the user. Only news written by the given user will be shown.
     *
     * @param int $id
     */
    public function filterUser(int $id): void;
}
