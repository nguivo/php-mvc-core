<?php

namespace framework\core;

use framework\core\middlewares\BaseMiddleware;

class Controller
{
    protected string $layout = 'main';

    /**
     *  @var BaseMiddleware[]
     */
    protected array $middlewares = [];
    public string $action = '';


    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }


    public function getLayout(): string
    {
        return $this->layout;
    }


    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }


    public function registerMiddleware(BaseMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }


    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

}