<?php

namespace Habr\Renat\UnitTests\Container;

class ClassDependingOnAnother {

    // Класс с двумя зависимостями
    public function __construct(
            private SomeClassWithoutDependencies $one,
            private SomeClassWithParameter $two,
//            private int $num
    ) {
        
    }

//    public  function num() {
//        return $this->num;
//    }

}
