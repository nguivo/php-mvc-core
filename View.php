<?php

namespace framework\core;

class View
{
    public string $title = 'pyramid';

    public function renderView(string $view, array $params = [])
    {
        $viewContent = $this->getViewContents($view, $params);
        $layoutContents = $this->getLayoutContents();

        return str_replace("{{content}}", $viewContent, $layoutContents);
    }


    public function getLayoutContents(): string
    {
        $layout = Application::$app->controller->getLayout();
        ob_start();
        include_once Application::$ROOT_DIR."/app/views/layouts/$layout.php";
        return ob_get_clean();
    }


    public function getViewContents($view, $params = []): string
    {
        if($params) {
            foreach($params as $key => $value) {
                $$key = $value;
            }
        }

        ob_start();

        if(file_exists(Application::$ROOT_DIR."/app/views/$view.php")) {
            include_once Application::$ROOT_DIR."/app/views/$view.php";
        }
        elseif (file_exists(Application::$ROOT_DIR."/app/views/authenticatedUser/$view.php")){
            include_once Application::$ROOT_DIR."/app/views/authenticatedUser/$view.php";
        }

        return ob_get_clean();
    }

}