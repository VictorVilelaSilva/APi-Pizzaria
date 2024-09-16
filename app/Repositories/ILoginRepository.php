<?php

namespace App\Repositories;

use App\Entities\LoginEntity;
use App\Entities\PersonEntity;

interface ILoginRepository
{
    public function Login(LoginEntity $entity): LoginEntity;

}
