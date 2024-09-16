<?php

namespace App\Repositories\CI4Model;

use App\Entities\LoginEntity;
use App\Models\ApiUsersModel;
use App\Repositories\ILoginRepository;

helper('utils');
class LoginRepository implements ILoginRepository
{
    public function Login(LoginEntity $entity): LoginEntity
    {
        $entity->uuid = generate_uuid();
        $entity->created_at = date('Y-m-d H:i:s');
        unset($entity->updatedAt);
        $apiUsersModel = new ApiUsersModel();
        $userData = $apiUsersModel->usernameExists($entity->username);
        badRequest($userData);



        return $entity;
    }
        
}