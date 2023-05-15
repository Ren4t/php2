<?php

namespace Habr\Renat\Blog\Repositories\UserRepository;

use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;

class InMemoryUsersRepository implements UsersRepositoryInterface {

    private array $users = [];

    public function save(User $user): void {
        $this->users[] = $user;
    }

    public function get(UUID $uuid): User {
        foreach ($this->users as $user) {
            if ((string) $user->uuid() === $uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }

}
