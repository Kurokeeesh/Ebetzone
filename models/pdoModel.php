<?php

function setDB(){
    static $pdo;

    if($pdo === null){
        //$pdo = new PDO("mysql:host=chaichx45.mysql.db; dbname=chaichx45","chaichx45","Charles63200");
        $configs = [
            ["dsn" => "mysql:host=localhost;port=8889;dbname=bdebetzone;charset=utf8mb4", "user" => "root", "password" => "root"],
            ["dsn" => "mysql:host=localhost;dbname=bdebetzone;charset=utf8mb4", "user" => "root", "password" => ""],
        ];

        $lastException = null;

        foreach ($configs as $config) {
            try {
                $pdo = new PDO($config["dsn"], $config["user"], $config["password"]);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        throw $lastException;
    }
    return $pdo;

}
