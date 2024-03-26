<?php

namespace framework\core;

use framework\core\exceptions\NotFoundException;

/**
 *  Class Router
 *
 * @package app\core
 * */
class Router
{
    protected array $routes = [];
    public Request $request;
    public Response $response;


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }


    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }


    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }


    public function resolve()
    {
        $path = $this->request->getRequestPath();
        $method = $this->request->getRequestMethod();

        Application::$app->controller = new Controller();

        $callback = $this->routes[$method][$path] ?? false;
        if($callback === false) {
            throw new NotFoundException("The Resource you are looking for is not found on this server");
        }

        if(is_string($callback)) {
            Application::$app->controller->action = $callback;
            return Application::$app->view->renderView($callback);
        }

        if(is_array($callback)) {
            /** @var Controller $foo */
            $foo = new $callback[0];
            Application::$app->controller = $foo;
            Application::$app->controller->action = $callback[1];
            $callback[0] = $foo;

            foreach ($foo->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }

        return call_user_func($callback, $this->request, $this->response);
    }


}