<?php

namespace App\Repositories\Users;

use App\Models\User;
use App\Repositories\Eloquent\EloquentRepository;

class UserEloquentRepository extends EloquentRepository implements UserRepositoryinterface
{

    /**
     * @return mixed
     */
    public function getModel()
    {
        return User::class;
    }
}
