<?php
declare(strict_types=1);

use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\UpdateUser;
use Kwai\Modules\Users\UseCases\UpdateUserCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can update a user', function () {
    $user = $this->withUser();
    $command = new UpdateUserCommand();
    $command->uuid = (string) $user->getUuid();
    $command->first_name = 'Jigoro';
    $command->last_name = 'Kano';
    $command->remark = "Updated with 'can update a user test'";
    $command->admin = $user->isAdmin();
    try {
        $user = UpdateUser::create(
            new UserDatabaseRepository($this->db)
        )($command, $this->withUser());
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
