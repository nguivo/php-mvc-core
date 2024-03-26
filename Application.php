<?php

namespace framework\core;

use app\controllers\SiteController;
use framework\core\db\Database;
use framework\core\db\DbModel;

/**
 *  Class Application
 *
 * @package app\core
 * */
class Application
{
    public static string $ROOT_DIR;

    public Session $session;
    public Request $request;
    public Response $response;
    public Router $router;
    public View $view;
    public Controller $controller; //value set in Router class
    public Database $db;
    public string $userClass;
    public ?UserModel $user;

    public static Application $app;


    public function __construct(string $rootPath, array $config)
    {
        $this->db = new Database($config['db']);

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->session = new Session();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->userClass = $config['userClass'];
        $this->user = new $this->userClass;

        $primaryValue = $this->session->get('user');

        if($primaryValue) {
            $primaryKey = $this->user->primaryKey();
            $this->user = $this->user->findOne([$primaryKey => $primaryValue]);
        }
    }


    public function run(): void
    {
        try {
            echo $this->router->resolve();
        }
        catch (\Exception $e) {
            $this->response->setResponseCode($e->getCode());
            echo $this->view->renderView('_errors', ['exception' => $e]);
        }

    }


    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }


    public static function isGuest(): bool
    {
        if(!array_key_exists('user', $_SESSION)) {
            return true;
        }
        return false;
    }


    public function logout(): void
    {
        $this->user = new $this->userClass;
        $this->session->delete('user');
    }


    public static function dnd($data): null
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";

        die();
    }


}