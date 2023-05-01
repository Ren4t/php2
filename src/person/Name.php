<?php

namespace Habr\Renat\Person;

class Name {
     
    public function __construct(
            private string $firstName,
            private string $lastName,
    ) {
        
    }
    public function first(): string {
        return $this->firstName;
    }

    public function last(): string {
        return $this->lastName;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

        
    public function __toString() {
        return $this->firstName . ' ' . $this->lastName;
    }

}
