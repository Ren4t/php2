<?php

namespace Habr\Renat\blog;

use Habr\Renat\person\Name;

class User {

    public function __construct(
            private int $id,
            private Name $username,
            private string $login,
    ) {
        
    }
    
    public function getId(): int {
        return $this->id;
    }
    public function getUsername(): Name {
        return $this->username;
    }

        
    public function __toString(): string{
        return "Юзер с id $this->id с именем $this->username и логином $this->login." . PHP_EOL;
    }

}
