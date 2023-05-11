<?php

namespace Habr\Renat\Http\Actions;

use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;

interface ActionInterface {
public function handle(Request $request): Response;
}
