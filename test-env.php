<?php

try {

    $pdo = new PDO(
        "mysql:host=".getenv('MYSQLHOST').
        ";port=".getenv('MYSQLPORT').
        ";dbname=".getenv('MYSQLDATABASE'),
        getenv('MYSQLUSER'),
        getenv('MYSQLPASSWORD')
    );

    echo "Connexion MySQL OK";

} catch(Exception $e) {

    echo $e->getMessage();

}