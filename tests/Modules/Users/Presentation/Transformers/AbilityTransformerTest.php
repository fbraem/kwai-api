<?php
/**
 * Testcase for AbilityTransformer
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Presentation\Transformers\AbilityTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform an ability', function () {
    $name = 'test';
    $remark = 'test';
    $traceableTime = new TraceableTime();
    $ability = new Ability(
        name: $name,
        remark: $remark,
        traceableTime: $traceableTime,
        rules: collect([])
    );
    $entity = new Entity(1, $ability);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(AbilityTransformer::createForItem($entity))
        ->toArray();

    expect($data)
        ->toBe([
            'data' => [
                'id' => 1,
                'name' => $name,
                'remark' => $remark,
                'created_at' => strval($traceableTime->getCreatedAt()),
                'updated_at' => null,
                'rules' => [
                    'data' => []
                ]
            ]
        ])
    ;
});
