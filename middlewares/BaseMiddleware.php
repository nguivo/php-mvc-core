<?php

namespace framework\core\middlewares;

abstract class BaseMiddleware
{

    abstract public function execute(): void;

}