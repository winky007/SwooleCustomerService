<?php

class MysqlPdo
{
    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDbConnection()
    {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PWD);
            return $pdo;
        } catch (Throwable $t) {
            throw new Exception($t->getMessage());
        }
    }

}
