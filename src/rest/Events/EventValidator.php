<?php
namespace REST\Events;

use Core\Validators\ValidatorInterface;
use Core\Validators\ValidationException;

use Carbon\Carbon;

class EventValidator implements ValidatorInterface
{
    public function validate($data)
    {
        $b = Carbon::createFromFormat('Y-m-d H:i', $data->start_date);
        $e = Carbon::createFromFormat('Y-m-d H:i', $data->end_date);
        if ($e->lt($b)) {
            throw new ValidationException([
                'data/attributes/end_time' => _('End date must be after start date')
            ]);
        }
    }
}
