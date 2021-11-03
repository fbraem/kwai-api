<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Club\Domain\Member;
use Kwai\Modules\Club\Presentation\Transformers\MemberTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

it('can transform a member', function ($team) {
    $fractal = new Manager();
    $fractal->setSerializer(new JsonApiSerializer());
    $resource = MemberTransformer::createForItem($team);
    $data = $fractal->createData($resource)->toArray();
    expect($data)
        ->toHaveKey('data.type', 'members')
        ->toHaveKey('data.attributes.name', 'Jigoro Kano')
    ;
})->with([
    new Entity(
        1,
        new Member(
            new Name('Jigoro', 'Kano')
        )
    )
]);
