<?php

namespace Habr\Renat\blog\repositories;

use Habr\Renat\blog\exceptions\UserNotFoundException;
use Habr\Renat\blog\User;

class InMemoryUserRepository {

    private array $users = [];

    public function save(User $user): void {
        $this->users[] = $user;
    }

    public function get(int $id): User {
        foreach ($this->users as $user){
            if($user->getId() === $id) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $id");
    }
}
