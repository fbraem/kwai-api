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

use Kwai\Modules\Users\Domain\Rule;

use Kwai\Modules\Users\Presentation\Transformers\RuleTransformer;

final class RuleTransformerTest extends TestCase
{
    public function testTransform()
    {
        $name = 'test';
        $remark = 'test';
        $action = 'create';
        $subject = 'team';
        $traceableTime = new TraceableTime();
        $rule = new Rule(
            (object) compact([
                'name',
                'remark',
                'action',
                'subject',
                'traceableTime'
            ])
        );
        $entity = new Entity(1, $rule);

        $fractal = new Manager();
        $fractal->setSerializer(new DataArraySerializer());
        $data = $fractal
            ->createData(RuleTransformer::createForItem($entity))
            ->toArray();

        $this->assertEquals(
            $data,
            [
                'data' => [
                    'id' => 1,
                    'name' => $name,
                    'action' => $action,
                    'subject' => $subject,
                    'remark' => $remark,
                    'created_at' => strval($traceableTime->getCreatedAt()),
                    'updated_at' => null
                ]
            ]
        );
    }
}
