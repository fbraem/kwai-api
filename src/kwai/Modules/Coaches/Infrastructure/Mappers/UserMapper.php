<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Coaches\Domain\User;

/**
 * Class UserDTO
 */
class UserMapper
{
    public static function toDomain(Collection $data): User
    {
        return new User(
            name: new Name($data->get('first_name'), $data->get('last_name'))
        );
    }

    public static function toPersistence(User $user): Collection
    {
        return collect([
            'first_name' => $user->getName()->getFirstName(),
            'last_name' => $user->getName()->getLastName()
        ]);
    }
}
