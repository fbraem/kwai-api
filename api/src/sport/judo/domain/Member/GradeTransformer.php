<?php

namespace Judo\Domain\Member;

use League\Fractal;

class GradeTransformer extends Fractal\TransformerAbstract
{
    public function transform(GradeInterface $grade)
    {
        return $grade->extract();
    }
}
