<?php
namespace REST\Teams;

use Kwai\Core\Infrastructure\Validators\ValidatorInterface;
use Kwai\Core\Infrastructure\Validators\ValidationException;

class TeamCategoryValidator implements \Kwai\Core\Infrastructure\Validators\ValidatorInterface
{
    public function validate($data)
    {
        if (isset($value->end_age) && isset($value->start_age)) {
            if ($value->end_age < $value->start_age) {
                throw new ValidationException([
                    'data/attributes/end_age' => 'end_age must be equal or greater then start_age'
                ]);
            }
        }
    }
}
