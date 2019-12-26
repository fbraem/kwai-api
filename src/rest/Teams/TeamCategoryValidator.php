<?php
namespace REST\Teams;

use Core\Validators\ValidatorInterface;
use Core\Validators\ValidationException;

class TeamCategoryValidator implements \Core\Validators\ValidatorInterface
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
