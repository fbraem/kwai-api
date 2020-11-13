<?php
namespace REST\Trainings;

use Carbon\Carbon;
use Core\Validators\ValidationException;
use Core\Validators\ValidatorInterface;

class TrainingValidator implements ValidatorInterface
{
    public function validate($data)
    {
        $b = Carbon::createFromFormat('Y-m-d H:i:s', $data->event->start_date);
        $e = Carbon::createFromFormat('Y-m-d H:i:s', $data->event->end_date);
        if ($e->lt($b)) {
            throw new ValidationException([
                'data/attributes/event/end_time' => _('End date must be after start date')
            ]);
        }
    }
}
