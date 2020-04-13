<?php
namespace REST\Trainings;

use Kwai\Core\Infrastructure\Validators\ValidatorInterface;
use Kwai\Core\Infrastructure\Validators\ValidationException;

use Carbon\Carbon;

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
