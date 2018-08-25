<?php
namespace REST\Teams;

use Psr\Http\Message\ResponseInterface as Response;
use Zend\Validator\Callback;
use Carbon\Carbon;

class TeamTypeValidator extends \Core\Validators\EntityValidator
{
    public function __construct()
    {
        parent::__construct();

        $endAgeValidator = new Callback(function ($value) {
            if (isset($value->end_age) && isset($value->start_age)) {
                return $value->end_age >= $value->start_age;
            }
            return true;
        });
        $endAgeValidator->setMessage(
            _('End age must be greater then start age'),
            Callback::INVALID_VALUE
        );

        $this->addValidator($endAgeValidator);
    }
}
