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
     * @return StoryQuery
     */
    public function filterId(int $id): self;

    /**
     * Add a filter on the publication date.
     * When month is omitted, all stories of the year will be returned.
     *
     * @param int      $year
     * @param int|null $month
     * @return StoryQuery
     */
    public function filterPublishDate(int $year, ?int $month): self;

    /**
     * Only select the promoted news stories.
     *
     * @return StoryQuery
     */
    public function filterPromoted(): self;

    /**
     * Add a filter on the application.
     *
     * @param int|string $appNameOrId
     * @return StoryQuery
     */
    public function filterApplication(int|string $appNameOrId): self;

    /**
     * Only select news stories that are enabled
     * and don't show news stories which are expired.
     *
     * @return StoryQuery
     */
    public function filterVisible(): self;

    /**
     * Filter on the user. Only news written by the given user will be shown.
     *
     * @param int $id
     * @return StoryQuery
     */
    public function filterUser(int $id): self;
}
