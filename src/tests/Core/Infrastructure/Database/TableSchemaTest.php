<?php

declare(strict_types=1);

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

    $storySchema->map(
        collect([
           'news_stories_id' => 1,
           'news_stories_title' => 'test'
        ])
    );
    expect($storySchema->id)->toBeInt()->toEqual(1);
    expect($storySchema->title)->toBeString()->toEqual('test');
    expect($storySchema->content_text)->toBeNull();
});

it('can handle an alias', function () {
    $storySchema = new StoryTableSchema('n');

    $storySchema->map(
        collect([
            'n_id' => 1,
            'n_title' => 'test'
        ])
    );
    expect($storySchema->id)->toBeInt()->toEqual(1);
    expect($storySchema->title)->toBeString()->toEqual('test');
    expect($storySchema->content_text)->toBeNull();
});
