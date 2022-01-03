<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema;

#[TableAttribute(name:'news_stories')]
class StoryTableSchema extends TableSchema
{
    public ?int $id = null;
    public string $title = '';
    public ?string $content_text = null;
}

it('can handle a default schema', function () {
    $storySchema = new StoryTableSchema();

    $result = $storySchema->map(
        collect([
           'news_stories_id' => 1,
           'news_stories_title' => 'test'
        ])
    );
    expect($result->id)->toBeInt()->toEqual(1);
    expect($result->title)->toBeString()->toEqual('test');
    expect($result->content_text)->toBeNull();
});

it('can handle an alias', function () {
    $storySchema = new StoryTableSchema('n');

    $result = $storySchema->map(
        collect([
            'n_id' => 1,
            'n_title' => 'test'
        ])
    );
    expect($result->id)->toBeInt()->toEqual(1);
    expect($result->title)->toBeString()->toEqual('test');
    expect($result->content_text)->toBeNull();
});

it('can map rows', function () {
    $result = StoryTableSchema::createFromRow(collect([
        'news_stories_id' => 1,
        'news_stories_title' => 'test'
    ]));
    expect($result->id)->toBeInt()->toEqual(1);
    expect($result->title)->toBeString()->toEqual('test');
    expect($result->content_text)->toBeNull();
});

it('can map a row with alias', function () {
    $result = StoryTableSchema::createFromRow(
        collect([
            'n_id' => 1,
            'n_title' => 'test'
        ]),
        'n'
    );

    expect($result->id)->toBeInt()->toEqual(1);
    expect($result->title)->toBeString()->toEqual('test');
    expect($result->content_text)->toBeNull();
});

it('can create aliases', function () {
    $aliases = StoryTableSchema::aliases();
    expect($aliases)
       ->toBeInstanceOf(Collection::class)
       ->and($aliases->toArray())
    ;
});
