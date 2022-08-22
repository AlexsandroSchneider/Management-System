<?php

    $dbHost = "localhost";
    $dbPort = "7438";
    $dbUser = "postgres";
    $dbPW = "postgres";
    $dbName = "newdb";
    
    $db_connect = pg_connect("host=$dbHost port=$dbPort user=$dbUser password=$dbPW dbname=$dbName") or die("Não foi possivel conectar.");

    return $db_connect;

?>