<?php

session_start();

/**
 * Класс авторизации.
 */

class Auth
{

    private $id;
    private $username;

    public function __construct()
    {



    }

    public function register($username, $password)
    {

        $message = '';

        $database = Database::getInstance();

        $username = trim($username);
        $password = password_hash($password, PASSWORD_BCRYPT);

        $user = $database->row("SELECT * FROM users WHERE username = :username", array('username' => $username));
        if ($user) {

            $message = 'Такой пользователь уже существует';
            $_SERVER['message_error'] = $message;

            header('Location: ' . $_SERVER['HTTP_REFERER']);

            return false;

        }

        $database->query('INSERT INTO users (username, password, created_at) VALUES (:username, :password, :created_at)', array(
            'username' => $username,
            'password' => $password,
            'created_at' => date('Y-m-d H:i:s'),
        ));

        $session_data = array(
            'id' => $database->lastId(),
            'username' => $username,
        );

        Session::push($session_data, 'auth_data', Session::SESSION_PUSH_ARRAY);
        $message = 'Пользователь успешно создан';

        Session::push($message, 'message_success');

        header('Location: ' . $_SERVER['HTTP_REFERER']);

    }

    public function login($username, $password)
    {
        $message = '';

        $database = Database::getInstance();

        $username = trim($username);
        $password = trim($password);

        $user = $database->row("SELECT * FROM users WHERE username = :username", array('username' => $username));
        if ($user) {

            if (password_verify($password, $user['password'])) {

                $session_data = array(
                    'id' => $user['id'],
                    'username' => $user['username'],
                );

                Session::push($session_data, 'auth_data', Session::SESSION_PUSH_ARRAY);

                header('Location: ' . 'index.php');

                return true;

            } else {

                $message = 'В нашей системе не найден такой пользователь';

                Session::push($message, 'message_error');

                header('Location: ' . $_SERVER['HTTP_REFERER']);

                return false;

            }

        }

    }

    public function logout()
    {



    }

}