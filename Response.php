<?php

namespace framework\core;

class Response
{

    public function setResponseCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $url): void
    {
        header("Location: ".$url);
        exit();
    }

}