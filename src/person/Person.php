<?php

namespace Habr\Renat\Person;

use \DateTimeImmutable;

class Person {
    
    public function __construct(
            private Name $name,
            private DateTimeImmutable $registeredOn,
    ) {
    }
    public function __toString() {
        return $this->name . ' (На сайте с ' . $this->registeredOn->format('Y-m-d') . ')';
    }
}
