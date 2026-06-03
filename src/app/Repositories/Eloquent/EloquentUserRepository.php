<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends AbstractEloquentRepository<User>
 */
class EloquentUserRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    protected function modelClass(): string
    {
        return User::class;
    }

    public function findOrCreateByEmail(string $email, string $name): User
    {
        /** @var User $user */
        $user = $this->model->newQuery()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(32)),
            ]
        );

        return $user;
    }
}
