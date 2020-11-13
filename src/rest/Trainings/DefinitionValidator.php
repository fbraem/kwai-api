<?php
namespace REST\Trainings;

use Carbon\Carbon;
use Core\Validators\ValidationException;
use Core\Validators\ValidatorInterface;

class DefinitionValidator implements ValidatorInterface
{
    public function validate($data)
    {
        $b = Carbon::createFromFormat('H:i:s', $data->start_time);
        $e = Carbon::createFromFormat('H:i:s', $data->end_time);
        if ($e->lt($b)) {
            throw new ValidationException([
                'data/attributes/end_time' => _('End time must be after start time')
            ]);
        }
    }
}
