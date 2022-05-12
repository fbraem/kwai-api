<?php
declare(strict_types=1);

use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\UpdateUser;
use Kwai\Modules\Users\UseCases\UpdateUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can update a user', function () use ($context) {
    $command = new UpdateUserCommand();
    $command->uuid = (string) $context->user->getUuid();
    $command->first_name = 'Jigoro';
    $command->last_name = 'Kano';
    $command->remark = "Updated with 'can update a user test'";
    try {
        $user = UpdateUser::create(
            new UserDatabaseRepository($context->db)
        )($command, $context->user);
        expect($user->getRemark())
            ->toEqual("Updated with 'can update a user test'")
        ;
    } catch (UnprocessableException $e) {
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (UserNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
