<?php

namespace framework\core;

class Request
{

    public function getRequestPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }


    public function getRequestMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }


    public function isGet(): bool
    {
        return $this->getRequestMethod() === 'get';
    }


    public function isPost(): bool
    {
        return $this->getRequestMethod() === 'post';
    }


/**
 *  function takes input data from forms and sanitizes it before we can use it.
 *
 * */
    public function getRequestBody(): array
    {
        $body = [];
        if($this->getRequestMethod() === 'get') {
            foreach($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }
        if($this->getRequestMethod() === 'post') {
            foreach($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}