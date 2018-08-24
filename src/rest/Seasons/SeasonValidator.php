<?php
namespace REST\Seasons;

use Psr\Http\Message\ResponseInterface as Response;
use Zend\Validator\Callback;
use Carbon\Carbon;

class SeasonValidator extends \Core\Validators\EntityValidator
{
    public function __construct()
    {
        parent::__construct();

        $endDateValidator = new Callback(function ($value) {
            $b = Carbon::createFromFormat('Y-m-d', $value->start_date);
            $e = Carbon::createFromFormat('Y-m-d', $value->end_date);
            return $e->gte($b);
        });
        $endDateValidator->setMessage(
            _('End date must be after start date'),
            Callback::INVALID_VALUE
        );

        $this->addValidator($endDateValidator);
    }
}
