<?php
namespace REST\Trainings;

use Core\Validators\ValidatorInterface;
use Core\Validators\ValidationException;

use Carbon\Carbon;

class DefinitionValidator implements ValidatorInterface
{
    public function validate($data)
    {
        $b = Carbon::createFromFormat('H:i', $data->start_time);
        $e = Carbon::createFromFormat('H:i', $data->end_time);
        if ($e->lt($b)) {
            throw new ValidationException([
                'data/attributes/end_time' => _('End time must be after start time')
            ]);
        }
    }
}
