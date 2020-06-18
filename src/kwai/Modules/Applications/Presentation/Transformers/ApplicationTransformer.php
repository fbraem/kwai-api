<?php

namespace Kwai\Modules\Applications\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Applications\Domain\Application;
use League\Fractal;

class ApplicationTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'applications';

    /**
     * @param Entity<Application> $app
     * @return Fractal\Resource\Item
     */
    public static function createForItem(Entity $app): Fractal\Resource\Item
    {
        return new Fractal\Resource\Item($app, new self(), self::$type);
    }

    /**
     * @param iterable $apps
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $apps): Fractal\Resource\Collection
    {
        return new Fractal\Resource\Collection($apps, new self(), self::$type);
    }

    /**
     * Transforms an application
     *
     * @param Entity<Application> $app
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public function transform(Entity $app)
    {
        $traceableTime = $app->getTraceableTime();

        return [
            'id' => $app->id(),
            'title' => $app->getTitle(),
            'name' => $app->getName(),
            'short_description' => $app->getShortDescription(),
            'description'=> $app->getDescription(),
            'remark' => $app->getRemark(),
            'weight' => $app->getWeight(),
            'news' => $app->canHaveNews(),
            'pages' => $app->canHavePages(),
            'events' => $app->canHaveEvents(),
            'created_at' => (string) $traceableTime->getCreatedAt(),
            'updated_at' => $traceableTime->isUpdated()
                ? (string) $traceableTime->getUpdatedAt() : null,
        ];
    }
}
