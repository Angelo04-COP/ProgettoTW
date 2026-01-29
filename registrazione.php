<!DOCTYPE html>
<html lang="it">
    <head>
        <title>MyCinema: REGISTRATI</title>
        <meta charset="utf-8" />

        <script type="text/javascript" src="validazione_registrazione.js" defer></script>
        <link rel="stylesheet" type="text/css" href="style_registrazione.css" />


    </head>

    <body>
        <?php
            $messaggio = "";

            if(isset($_POST['nome']))
                $nome = $_POST['nome'];
            else    
                $nome= "";

            if(isset($_POST['cognome']))
                $cognome = $_POST['cognome'];
            else
                $cognome = "";

            if(isset($_POST['username']))
                $user = $_POST['username'];
            else
                $user = "";

            if(isset($_POST['email']))
                $email = $_POST['email'];
            else
                $email= "";

            if(isset($_POST['password']))
                $pass = $_POST['password'];
            else    
                $pass = "";

            if(isset($_POST['repassword']))
                $repassword = $_POST['repassword'];
            else
                $repassword = "";

            //Se il campo della password non è vuoto
            if(!empty($pass)){
                //controllo se l'utente già esiste
                if(username_exist($user)){
                   $messaggio = "<p id='error-message'> L'username $user già esiste. Riprova<p>";

                }else{
                    //se non esiste, inserisco il nuovo utente nel database e ottengo il relativo ID
                    $user_id = insert_utente($nome, $cognome, $user, $email, $pass);
                    if($user_id){
                       $messaggio = "<p id='success-message'> Utente registrato con successo. Effettua il <a href=\"login.php\">login</a> oppure ritorna alla Home</p>";

                    }else{
                        $messaggio = "<p id='error-message'> Si è verificato un errore durante la registrazione. Riprova <p>";
                    }

                }
            }
        ?>


        <div class="reg-container">
            <div class="header">
                <h1>My<span>CINEMA</span></h1>
                <p id="header-par">Compila i campi a lato per registrarti</p>

            </div>
            <div class="reg-box">
                <form onsubmit = "return validaModulo(this);" method="post" action = "registrazione.php">
                    <div class="input-group">
                        <input type="text" name="nome" id="nome" placeholder="Nome" value="<?php echo $nome ?>" />
                    </div>
                    <div class="input-group">
                        <input type="text" name="cognome" id="cognome" placeholder="Cognome" value="<?php echo $cognome ?>"/>

                    </div>
                    <div class="input-group">
                        <input type="text" name="username" id="username" placeholder="Username" value="<?php echo $user ?>" />
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" id="email" placeholder="user@domain.com" value="<?php echo $email ?>"/>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Password (Min. 6 caratteri)" value="<?php echo $pass ?>"/>

                    </div>
                    <div class="input-group">
                        <input type="password" name="repassword" id="repassword" placeholder="Ripeti la password" value="<?php echo $repassword ?>"/>

                    </div>

                    <input id="reg-button" type="submit" name="registra" value= "Registrati" />
                
                    <?php echo $messaggio ?>

                </form>
                <div class="returnHome">
                    <a href="index.php">&larr; Torna alla Home</a>
                </div> 
            </div>

        </div>

    </body>
</html>

<?php
    function username_exist($user){
        require "db.php";


        //query sql
        $sql = "SELECT username FROM account WHERE username=$1";


        //creo il prepared statement mediante la funzione pg_prepare: 
        //primo parametro --> la risorsa connection al database
        // secondo parametro --> il nome da assegnare al prepared statement
        //terzo parametro --> statement SQL parametrizzato. I parametri devono essere indicati usando i placeholder $1, $2, $3,....

        $prep = pg_prepare($connect, "sqlUsername", $sql);

        //eseguo il prepared statement mediante la funzione pg_execute
        //primo parametro --> risorsa Connection al database
        //secondo parametro --> il nome del prepared statement da eseguire
        //terzo parametro --> array di valori da sostituire al placeholder

        $ret = pg_execute($connect, "sqlUsername", array($user));
        if(!$ret){
            echo "ERRORE QUERY: " . pg_last_error($connect);
            return false;

        }else{

            //utilizzo la funzione pg_fetch_assoc che restituisce la prossima riga del risultato di una query come array associativo
            //Come parametri riceve:
            // primo parametro --> la risorsa contenente i risultati della query;
            // secondo parametro (opzionale) --> il numero della riga a cui vogliamo accedere. Se omesso, ad ogni chiamata viene restituita la riga successiva.
            //Come risultato restituisce un array associativo in cui le chiavi sono i nomi dei campi della query.
            //Restituisce falso se il numero di riga ($row) specificato è maggiore del numero di righe presenti in 
            //$ret; se non ci sono altre righe; o in caso di errore
            if($row = pg_fetch_assoc($ret)){
                return true;
            }else{
                return false;
            }

        }
    }

    function insert_utente($nome, $cognome, $user, $email, $pass){
        require "db.php";

        //utilizzo la funzione password_hash per convertire la password in una 
        // stringa complessa, ovvero in un hash. La funzione riceve come parametro:
        // - primo parametro --> la password
        // - secondo parametro --> il tipo di algoritmo di HASHING che si vuole utilizzare
        // Specifico come secondo parametro la costante PASSWORD_DEFAULT, per fare in modo che venga sempre
        //utilizzato l'algoritmo di hashing più sicuro attualmente implementato in PHP  
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        //creo il prepared statement che deve ritornare l'ID dell'utente (si utilizza la clausola RETURNING)
        $sql = 'INSERT INTO account(nome, cognome, username, email, password) VALUES($1, $2, $3, $4, $5) RETURNING id';
        $prep = pg_prepare($connect, "insertUser", $sql);

        //eseguo il prepared statement
        $ret = pg_execute($connect, "insertUser", array($nome, $cognome, $user, $email, $hash));
        if(!$ret){
            echo "ERRORE QUERY: " .pg_last_error($connect);
            return false;
        }else{
            //si recupera l'ID restituito dalla clausola RETURNING
            $row = pg_fetch_assoc($ret);
            return $row['id'];
        }

    }

?>