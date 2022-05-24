<?php
declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
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
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);

beforeEach(fn() => $this->withDatabase());

it('can create a story', function () {
    // This test will create a promoted story.
    try {
        $application = (new ApplicationDatabaseRepository($this->db))->getById(1);
    } catch (ApplicationNotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
    $author = new Creator(
        1,
        new Name(
            'Jigoro',
            'Kano'
        )
    );
    $repo = new StoryDatabaseRepository($this->db);
    try {
        $story = $repo->create(new Story(
            publishTime: new LocalTimestamp(Timestamp::createNow(), 'Europe/Brussels'),
            application: $application,
            contents: collect([
                    new Text(
                        Locale::NL,
                        DocumentFormat::MARKDOWN,
                        'Unit Test',
                        $author,
                        'Summary for Unit Test',
                        'Content for **Unit** Test',
                    )
                ]),
            promotion: new Promotion(1),
            enabled: true
        ));
        expect($story)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($story->domain())
            ->toBeInstanceOf(Story::class)
        ;
        return $story->id();
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->fail((string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can retrieve a story', function (int $id) {
        $repo = new StoryDatabaseRepository($this->db);
    try {
        $story = $repo->getById($id);
        expect($story)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($story->domain())
            ->toBeInstanceOf(Story::class)
        ;
    } catch (StoryNotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a story')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can query stories', function () {
    $repo = new StoryDatabaseRepository($this->db);
    $query = $repo->createQuery();
    $query->filterPromoted();
    try {
        $stories = $query->execute();
        expect($stories)
            ->toBeInstanceOf(Collection::class)
        ;
        expect(count($stories))
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a story')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
