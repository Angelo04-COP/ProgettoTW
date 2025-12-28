<!DOCTYPE html>
<html lang="it">
    <head>
        <title>MyCinema: REGISTRATI</title>
        <meta charset="utf-8" />

        <style>
            
            body{
                display: flex;   /*Visualizza l'elemento come un contenitore "flessibile" */
                /*gli elementi della pagina sono disposti uno sotto l'altro (header, content e footer)*/
                background-color: black;
                justify-content: center;
                padding: 100px;




            }


            .reg-container{
                display: flex;             /*Visualizza l'elemento come un contenitore "flessibile" */
                justify-content: center;  /*centra il div*/
                align-items: center;      /*allinea gli elementi del div al centro*/
                padding: 20px;
                background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);  /*colore di sfondo*/





            }

            .header{
                text-align: center;
                margin-bottom: 30px;
            }

            h1{
                color: #ff9d00;
            }

            #header-par{
                color: #666;
                margin-top: 10px;


            }



            /*Box centrale per l'autenticazione*/
            .reg-box{
                background-color: #111;
                padding: 80px;
                width: 100%;
                border: 1px solid #222;

            }

            /*Elementi del form*/
            .input-group{
                margin-bottom: 20px;
                margin-right: 50px;

            }
            
            input{
                background-color: #222;
                color: white;
                border: 1px solid #333;
                width: 100%;
                padding: 20px;
            
            }

            #reg-button{
                background-color: #ff9d00;
                color: black;
                padding: 15px;
                width: 100px;
                font-weight: bold;
                cursor: pointer;


            }

            #reg-button:hover{
                background-color: #e68a00;


            }

            p{
                margin-top: 25px;
                text-align: center;
                color: #888;

            }

            p > a{
                color: #ff9d00;
                text-decoration: none;
                font-weight: bold;

            }

            #error-message{ 
                color: #ff4444;
                font-weight: bold;
                margin-top: 15px;
                text-align: center;
            }

            #success-message{ 
                color: #00c851;
                font-weight: bold;
                margin-top: 15px;
                text-align: center;
            }

            














        </style>


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
                    //se non esiste, inserisco il nuovo utente nel database
                    if(insert_utente($nome, $cognome, $user, $email, $pass)){
                       $messaggio = "<p id='success-message'> Utente registrato con successo. Effettua il <a href=\"login.php\">login</a></p>";

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
                <form method="post" action = "registrazione.php">
                    <div class="input-group">
                        <input type="text" name="nome" id="nome" placeholder="Nome" value="<?php echo $nome ?>" required/>
                    </div>
                    <div class="input-group">
                        <input type="text" name="cognome" id="cognome" placeholder="Cognome" value="<?php echo $cognome ?>" required />

                    </div>
                    <div class="input-group">
                        <input type="text" name="username" id="username" placeholder="Username" value="<?php echo $user ?>" required />
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" id="email" placeholder="user@domain.com" value="<?php echo $email ?>" required />
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Password (Min. 6 caratteri)" value="<?php echo $pass ?>" required />

                    </div>
                    <div class="input-group">
                        <input type="password" name="repassword" id="repassword" placeholder="Ripeti la password" value="<?php echo $repassword ?>" required />

                    </div>

                    <input id="reg-button" type="submit" name="registra" value= "Registrati" />
                
                    <?php echo $messaggio ?>

                </form>


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

        //creo il prepared statement
        $sql = 'INSERT INTO account(nome, cognome, username, email, password) VALUES($1, $2, $3, $4, $5)';
        $prep = pg_prepare($connect, "insertUser", $sql);

        //eseguo il prepared statement
        $ret = pg_execute($connect, "insertUser", array($nome, $cognome, $user, $email, $hash));
        if(!$ret){
            echo "ERRORE QUERY: " .pg_last_error($connect);
            return false;
        }else{
            return true;
        }

    }



?>