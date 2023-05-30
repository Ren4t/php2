<?php

namespace Habr\Renat\Blog;

use Habr\Renat\Person\Name;

class User {

    public function __construct(
            private UUID $uuid,
            private Name $name,
            private string $username,
            private string $hashedPassword
    ) {
        
    }

    public function uuid(): UUID {
        return $this->uuid;
    }

    public function name(): Name {
        return $this->name;
    }

    public function username(): string {
        return $this->username;
    }

    public function hashedPassword(): string {
        return $this->hashedPassword;
    }

    // Функция для вычисления хеша
    private static function hash(string $password, UUID $uuid): string {
        return hash('sha256', (string) $uuid . $password);
    }

// Функция для создания нового пользователя
    public static function createFrom(
            string $username,
            string $password,
            Name $name
    ): self {
        $uuid = UUID::random();
        return new self(
                $uuid,
                $name,
                $username,
                self::hash($password, $uuid)
        );
    }

    public function checkPassword(string $password): bool {
        // Передаём UUID пользователя
// в функцию хеширования пароля
        return self::hash($password, $this->uuid) === $this->hashedPassword;
    }

    public function __toString(): string {
        return "Юзер с id $this->uuid с именем $this->name и логином $this->username." . PHP_EOL;
    }

}
