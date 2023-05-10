<?php

namespace Habr\Renat\UnitTests\Blog;

use PHPUnit\Framework\TestCase;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;

class UserTest extends TestCase{
    
    private function createUser(): User {
        return new User(
                    new UUID('af1aa517-060e-4f8d-bb43-3ebb4035dd82'),
                    new Name('Ivan','Ivanov'),
                    'admin'
            );
    }


    public function testGetUuid(): void {
        $user = $this->createUser();
        
        $value = $user->uuid();
        $this->assertEquals('af1aa517-060e-4f8d-bb43-3ebb4035dd82', $value);
        
    }
    public function testGetName(): void {
        $user = $this->createUser();
        
        $value = $user->name();
        $this->assertEquals('Ivan Ivanov', $value);
    }
    public function testGetUsername(): void {
       
        $user = $this->createUser();
        
        $value = $user->username();
        $this->assertEquals('admin', $value);
    }

        
    public function testUserToString(): void{
        $user =$this->createUser();
        
        $value = (string)$user;
        $this->assertEquals('Юзер с id af1aa517-060e-4f8d-bb43-3ebb4035dd82 с именем Ivan Ivanov и логином admin.' . PHP_EOL, $value);
    }
}
