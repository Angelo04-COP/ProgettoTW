<?php
    $host='localhost';
    $port='5432';
    $db= 'gruppo15';
    $username = 'www';
    $password = 'www';

    $connection_string = "host=$host port=$port dbname=$db user=$username password=$password";

    //pg_connect(string $connection_string) : resource
    //restituisce una risorsa connection in caso di successo , oppure
    //false in caso di fallimento.
    //parametro: connection_string --> stringa di connessione al database; contiene i parametri per connettersi al 
    // database, ed è formata da coppie keyword = value separate da spazio

    $connect = pg_connect($connection_string) or die('Impossibile connettersi al database: ' . pg_last_error());



?>