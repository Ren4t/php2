<?php

namespace Habr\Renat\Http\Actions\Users;

use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Actions\ActionInterface;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;
use Habr\Renat\Http\SuccessfulResponse;
use Habr\Renat\Person\Name;

class CreateUser implements ActionInterface {

    public function __construct(
            private UsersRepositoryInterface $usersRepository
    ) {
        
    }

    public function handle(Request $request): Response {
        try {
            $newUserUuid = UUID::random();
            $user = new User(
                    $newUserUuid,
                    new Name(
                            $request->jsonBodyField('first_name'),
                            $request->jsonBodyField('last_name')
                    ),
                    $request->jsonBodyField('username')
            );
        } catch (HttpException $ex) {
            return new ErrorResponse($ex->getMessage());
        }
        
        $this->usersRepository->save($user);
        
        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid
        ]);
    }

}
