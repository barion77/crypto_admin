<?php

/**
 *
 * Класс для взаимодействия с mysql.
 * Используется библиотека PDO.
 *
 * https://www.php.net/manual/ru/book.pdo.php
 *
 */

class Database
{

    public $connection;
    public static $_instance;

    /**
     * Конструктор.
     * Создание соединение с базой.
     */

    public function __construct()
    {

        $config = require_once '../config.php';
        $host = $config['DB_HOST'];
        $database = $config['DB_DATABASE'];
        $username = $config['DB_USERNAME'];
        $password = $config['DB_PASSWORD'];

        $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);

    }

    /**
     * Метод для исполнения sql запроса.
     *
     * @param $sql - sql запрос
     * @param $params - параметры запроса
     * @return false|PDOStatement
     */

    public function query($sql, $params = [])
    {

        $stmt = $this->connection->prepare($sql);
        if (!empty($params) && is_array($params)) {

            foreach ($params as $key => $value)
                $stmt->bindValue(':' . $key, $value);

        }

        $stmt->execute();

        return $stmt;

    }

    /**
     * Метод для получение записей из базы.
     *
     * @param $sql - sql запрос
     * @param $params - параметры запроса
     * @return array|false
     */

    public function row($sql, $params = [])
    {

        $query = $this->query($sql, $params);
        $records = $query->fetchAll(PDO::FETCH_ASSOC);

        return $records;

    }

    public function lastId()
    {

        $id = $this->connection->lastInsertId();

        return $id;

    }

    /**
     * Миграция таблиц.
     *
     * @return void
     */

    public function migrate()
    {
        $sql_queries = array();

        // Создание таблицы users
        $schema_users = 'users';
        $sql_queries[] = "CREATE TABLE if NOT EXISTS $schema_users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
            username VARCHAR(255) NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            created_at TIMESTAMP NOT NULL)";

        // Создание таблицы user_settings
        $schema_user_settings = 'user_settings';
        $sql_queries[] = "CREATE TABLE if NOT EXISTS $schema_user_settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
            user_id BIGINT UNSIGNED NOT NULL, 
            title VARCHAR(255) DEFAULT 'Airdrops on TrustPad, The Exclusive Multi-Chain Airdrops',
            gift_name VARCHAR(255) DEFAULT 'PancakeSwap', 
            gift_buy_button_text VARCHAR(255) DEFAULT 'Buy TPAD',
            logo VARCHAR(255) DEFAULT 'logo.png', 
            logo_title VARCHAR(255) DEFAULT 'Biswap', 
            favicon VARCHAR(255) DEFAULT 'favicon.ico', 
            description TEXT DEFAULT 'Biswap is a decentralized exchange (DEX) that allows users to swap tokens on the BNB Smart Chain. Besides having a novel referral system and low trading fees, Biswap also offers an assortment of products and services.', 
            amount_airdrop INT DEFAULT 500, 
            slider_all_money INT DEFAULT 3000, 
            slider_current_money INT DEFAULT 2750,
            meta_description TEXT DEFAULT 'TrustPad is a decentralized multi-chain fundraising platform enabling projects to raise capital and promise safety to early stage investors. Stake TrustPad tokens to get priority-access to promising projects.',
            meta_img VARCHAR(255) DEFAULT 'metaimg.png',
            created_at TIMESTAMP NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users (id))";

        foreach ($sql_queries as $sql)
            $this->query($sql);

    }

    /**
     * Singleton.
     *
     * @return Database
     */

    public static function getInstance()
    {

        if (self::$_instance instanceof self)
            return self::$_instance;

        return self::$_instance = new self;

    }

}