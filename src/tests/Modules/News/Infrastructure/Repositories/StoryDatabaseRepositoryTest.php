<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

beforeAll(function () use ($context) {
    if (Context::hasDatabase()) {
        $context->author = new Creator(
            1,
            new Name(
                'Jigoro',
                'Kano'
            )
        );

        try {
            $context->application = (new ApplicationDatabaseRepository($context->db))->getById(1);
        } catch (ApplicationNotFoundException $e) {
        } catch (RepositoryException $e) {
        }
    }
});

it('can create a story', function () use ($context) {
    // This test will create a promoted story.
    if (! isset($context->application)) {
        $this->assertTrue(false, 'No application');
    }
    $repo = new StoryDatabaseRepository($context->db);
    try {
        $story = $repo->create(new Story(
            (object)[
                'enabled' => true,
                'promotion' => new Promotion(1),
                'publishTime' => Timestamp::createNow('Europe/Brussels'),
                'application' => $context->application,
                'contents'=> [
                    new Text(
                        new Locale('nl'),
                        new DocumentFormat('md'),
                        'Unit Test',
                        'Summary for Unit Test',
                        'Content for **Unit** Test',
                        $context->author
                    )
                ]
            ]
        ));
        expect($story)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($story->domain())
            ->toBeInstanceOf(Story::class)
        ;
        return $story;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    }
    return null;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it(
    'can retrieve a story',
    function (Entity $lastCreated) use ($context) {
        // Skip test if testCreate failed.
        if ($lastCreated == null) {
            return;
        }

        $repo = new StoryDatabaseRepository($context->db);
        try {
            $story = $repo->getById($lastCreated->id());
            expect($story)
                ->toBeInstanceOf(Entity::class)
            ;
            expect($story->domain())
                ->toBeInstanceOf(Story::class)
            ;
        } catch (StoryNotFoundException $e) {
            $this->assertTrue(false, (string) $e);
        } catch (RepositoryException $e) {
            $this->assertTrue(false, (string) $e);
        }
    }
)
    ->depends('it it can create a story')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can query stories', function () use ($context) {
    $repo = new StoryDatabaseRepository($context->db);
    $query = $repo->createQuery();
    $query->filterPromoted();
    try {
        $stories = $query->execute();
        expect($stories)
            ->toBeArray()
        ;
        expect(count($stories))
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->assertTrue(false, $e->getMessage());
    }
})
    ->depends('it it can create a story')
    ->skip(!Context::hasDatabase(), 'No database available')
;
