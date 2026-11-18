<?php

function setDB(){
    static $pdo;

    if($pdo === null){
        //$pdo = new PDO("mysql:host=chaichx45.mysql.db; dbname=chaichx45","chaichx45","Charles63200");
        $configs = [
        // Connexion en ligne AlwaysData
        [
            "dsn" => "mysql:host=mysql-gaby.alwaysdata.net;port=3306;dbname=gaby_bdebetzone;charset=utf8mb4",
            "user" => "gaby", // À remplacer si votre utilisateur MySQL s'appelle autrement (ex: gaby_admin)
            "password" => "3QGZ4*f8*@n6cvMt*q%z" // Mettez ici le mot de passe de votre utilisateur MySQL AlwaysData
        ],
        // Connexions locales (MAMP) gardées en secours
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
