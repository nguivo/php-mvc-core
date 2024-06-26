<?php

namespace framework\core;

class Session
{
    protected const FLASH_KEY = 'flash';

    public function __construct()
    {
        session_start();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }


    public function setFlash($key, $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }


    public function getFlash($key): string
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? '';
    }


    public function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }


    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }


    public function delete($key): void
    {
        unset($_SESSION[$key]);
    }


    public function __destruct()
    {
        // removed all flash messages marked to be removed.
        $flashMessages = $_SESSION[self::FLASH_KEY];
        foreach($flashMessages as $key => &$flashMessage) {
            if($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}