<?php


namespace App\Http\Manager;


use App\Http\Entity\UserEntity;
use App\Model\Chain;
use App\User;

class UserManager
{
    public function map(User $data)
    {
        $user = new UserEntity();


        return $user;
    }
}