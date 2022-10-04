<?php

session_start();

/**
 * Класс для взаимодействии с сессией.
 */

class Session
{

    const SESSION_PUSH_ARRAY = 1;
    const SESSION_POP_AND_DELETE = 2;

    /**
     * Метод для сохранения данных в сессии.
     *
     * @param $value - значение которое занесется в сессию
     * @param $key - ключ под которым будет находиться значение
     * @param $flag
     * @return void
     */

    public static function push($value, $key, $flag = 0)
    {

        if ($flag)
            $value = json_encode($value);

        $_SESSION[$key] = $value;

    }

    /**
     * Метод для получение значения из сессии
     *
     * @param $key - ключ значения в сессии.
     * @param $flag
     * @return string $value
     */

    public static function pop($key, $flag = 0)
    {

        if (!isset($_SESSION[$key]))
            return NULL;

        $value = $_SESSION[$key];

        if ($flag == self::SESSION_POP_AND_DELETE)
            unset($_SESSION[$key]);

        return $value;

    }

}