<?php

namespace Domain\User;

use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
    {
        return $user->extract();
    }
}
