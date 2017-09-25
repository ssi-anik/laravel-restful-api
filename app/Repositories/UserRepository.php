<?php namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    private $model = null;

    public function __construct (User $user)
    {
        $this->model = $user;
    }

    public function saveNewUser (array $data)
    {
        return $this->model->create($data);
    }
}