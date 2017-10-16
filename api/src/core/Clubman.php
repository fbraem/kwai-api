<?php

namespace Core;

class Clubman
{
    private static $application;

    public static function getApplication()
    {
        if (self::$application == null) {
            self::$application = new Application(new \Zend\Config\Config(include __DIR__ . '/../../config.php'));
        }
        return self::$application;
    }
}
