<?php

namespace framework\core\middlewares;

use framework\core\Application;
use framework\core\exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];


    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }
    

    public function execute(): void
    {
        if(Application::isGuest()) {
            if(empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}