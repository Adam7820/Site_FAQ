<?php

function db_connect() {
    try {
        $host = "localhost";
        $dbname = "coding_faq";
        $user = "root";
        $password = "root";

        $db = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $password,
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::MYSQL_ATTR_DIRECT_QUERY => true
            )
        );

        return $db;
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

$db = db_connect();
?>
