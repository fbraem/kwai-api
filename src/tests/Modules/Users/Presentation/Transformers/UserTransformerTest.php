<?php
/**
 * Testcase for UserTransformer
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Presentation\Transformers;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform a user', function () {
    $uuid = new UniqueId();
    $email = new EmailAddress('test@kwai.com');
    $remark = 'test';
    $traceableTime = new TraceableTime();
    $user = new User((object)[
        'uuid' => $uuid,
        'emailAddress' => $email,
        'remark' => $remark,
        'traceableTime' => $traceableTime
    ]);
    $entity = new Entity(1, $user);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(UserTransformer::createForItem($entity))
        ->toArray();

    expect($data)
        ->toBe([
            'data' => [
                'id' => strval($uuid),
                'email' => strval($email),
                'username' => '',
                'remark' => 'test',
                'created_at' => strval($traceableTime->getCreatedAt()),
                'updated_at' => null
            ]
        ])
    ;
});
