<?php
    /*si attiva la sessione*/
    session_start();

    /*si distruggono le variabili di sessione*/
    session_unset();

    /*si distrugge la sessione*/
    session_destroy();

    //reindirizza il browser dell'utente all'URL "index.php"
    //la funzione header serve ad inviare header HTTP grezzi (metadati) dal server al browser del visitatore
    //Questi messaggi indicano al browser come comportarsi prima ancora che il contenuto della pagina venga caricato

    header("Location: index.php");

?>