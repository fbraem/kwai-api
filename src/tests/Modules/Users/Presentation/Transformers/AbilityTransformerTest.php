<?php
/**
 * Testcase for Password
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\Ability;

use Kwai\Modules\Users\Presentation\Transformers\AbilityTransformer;

final class AbilityTransformerTest extends TestCase
{
    public function testTransform()
    {
        $name = 'test';
        $remark = 'test';
        $traceableTime = new TraceableTime();
        $ability = new Ability((object)[
            'name' => $name,
            'remark' => $remark,
            'traceableTime' => $traceableTime,
            'rules' => []
        ]);
        $entity = new Entity(1, $ability);

        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer());
        $data = $fractal
            ->createData(AbilityTransformer::createForItem($entity))
            ->toArray();

        $this->assertEquals(
            $data,
            [
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
            ]
        );
    }
}