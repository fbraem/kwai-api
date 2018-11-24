<?php
namespace REST\Trainings;

use Core\Validators\ValidatorInterface;
use Core\Validators\ValidationException;

use Carbon\Carbon;

class EventValidator implements ValidatorInterface
{
    public function validate($data)
    {
        $b = Carbon::createFromFormat('Y-m-d H:i:s', $data->start_date . ' ' . $data->start_time);
        $e = Carbon::createFromFormat('Y-m-d H:i:s', $data->start_date . ' ' . $data->end_time);
        if ($e->lt($b)) {
            throw new ValidationException([
                'data/attributes/end_time' => _('End date must be after start date')
            ]);
        }
    }
}
