<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * @extends BaseRepositoryInterface<User>
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param string $email
     * @param string $name
     * @return User
     */
    public function findOrCreateByEmail(string $email, string $name): User;
}
