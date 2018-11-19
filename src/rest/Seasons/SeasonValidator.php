<?php
namespace REST\Seasons;

use Core\Validators\ValidatorInterface;
use Core\Validators\ValidationException;

use Carbon\Carbon;

class SeasonValidator implements ValidatorInterface
{
    public function validate($data)
    {
        $b = Carbon::createFromFormat('Y-m-d', $data->start_date);
        $e = Carbon::createFromFormat('Y-m-d', $data->end_date);
        if ($e->lt($b)) {
            throw new ValidationException([
                'data/attributes/end_date' => _('End date must be after start date')
            ]);
        }
    }
}
