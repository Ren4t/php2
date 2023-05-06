<?php

namespace Habr\Renat\Blog\Repositories\UserRepository;

use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;

interface UsersRepositoryInterface {

    public function save(User $user): void;

    public function get(UUID $uuid): User;
    
    public function getByUsername(string $username): User;
}
