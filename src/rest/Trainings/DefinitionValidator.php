<?php
namespace REST\Trainings;

use Kwai\Core\Infrastructure\Validators\ValidatorInterface;
use Kwai\Core\Infrastructure\Validators\ValidationException;

use Carbon\Carbon;

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
