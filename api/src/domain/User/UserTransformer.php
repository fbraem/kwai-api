<?php

namespace Domain\User;

use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
    {
        $lastLogin = $user->last_login;
        if ($lastLogin != null) {
            $lastLogin = (string) $lastLogin;
        }

        return [
            'id' => (int) $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'remark' => $user->remark,
            'last_login' => $lastLogin,
            'created_at' => (string) $user->created_at,
            'updated_at' => (string) $user->updated_at
        ];
    }
}
