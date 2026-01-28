<?php
    include 'db.php';
    session_start();
?>
<html lang="it">
    <head>
        <title>MyCinema : ACCEDI</title>
        <meta charset="utf-8">

        <script type="text/javascript" src="validazione_login.js" defer ></script>

        <link rel="stylesheet" type="text/css" href="style_login.css" />



    </head>

    <body>
        <?php
              $messaggio = "";
              if(!empty($_POST['username']) && !empty($_POST['password'])){
                $user = $_POST['username'];
                $pass = $_POST['password'];
              

              //utilizzo la funzione get_pwd che controlla se la password dell'utente $user è presente nel database
              $user_data = get_user_data($user, $connect); 

              if(!$user_data){
                //utilizzo htmlspecialchars per evitare che qualcuno possa iniettare script malevoli nel campo di input
                $safe_user = htmlspecialchars($user);
                $messaggio = "<p id= 'error-message'> L'utente $safe_user non esiste. <a href=\"login.php\"> Riprova </a> </p>";


              }else{
                if(password_verify($pass, $user_data['password'])){
                    $messaggio = "<p id = 'success-message' > Login effettuato con successo. Ritorna alla Home </p>";
                    //salvo nella sessione corrente i dati dell'utente
                    $_SESSION['id'] = $user_data['id'];
                    $_SESSION['nome'] = $user_data['nome'];
                    $_SESSION['cognome'] = $user_data['cognome'];
                    $_SESSION['username'] = $user_data['username'];
                    $_SESSION['email'] = $user_data['email'];

                    //Controllo se l'utente autenticato ha giò attivi i piani aggiunti nel carrello
                    // Cerco i piani che l'utente ha GIÀ acquistato e sono attivi
                    $query_check = "SELECT id_piano FROM abbonamenti 
                    WHERE id_utente = $1 
                    AND stato = 'attivo' 
                    AND data_fine >= CURRENT_DATE";
    
                    // uso l'ID appena recuperato dal database ($user_data['id'])
                    $res_check = pg_query_params($connect, $query_check, array($user_data['id']));
    
                    if ($res_check) {
                    $piani_gia_attivi = pg_fetch_all_columns($res_check) ?: [];

                        // Se c'è qualcosa nel carrello (messo da anonimo), filtro
                        if (isset($_SESSION['carrello']) && !empty($_SESSION['carrello'])) {
                            foreach ($_SESSION['carrello'] as $key => $item) {
                            // Se l'abbonamento nel carrello è già tra quelli attivi nel DB, lo togliamo
                                if (in_array($item['id'], $piani_gia_attivi)) {
                                  unset($_SESSION['carrello'][$key]);
                                }
                            }
                            // Re-indicizzo l'array per evitare "buchi" nelle chiavi
                            $_SESSION['carrello'] = array_values($_SESSION['carrello']);
                        }
                    }
                }else{
                    $messaggio = "<p id = 'error-message'> Username o password errati. <a href=\"login.php\">Riprova </a></p>";
                }
               }
            }
        ?>

        <div class="auth-container">
            <div class="header">
                <h1>My<span>CINEMA</span></h1>
                <p id="header-par">Compila i campi a lato per accedere</p>

            </div>
            <div class="auth-box">
                <form onsubmit = "return validaLogin(this);" method="post" action = "login.php">
                    <div class="input-group">
                        <input type="text" name="username" id="username" placeholder="Username"/>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Password"/>
                    </div>

                    <input id="auth-button" type="submit" name="Invia" value="Accedi" />
                </form>


                <p>Sei un nuovo utente? <a href="registrazione.php">Registrati</a></p>

                <?php
                    echo $messaggio;
                ?>

                <div class = "returnHome">
                    <a href="index.php">&larr; Torna alla Home</a>

                </div>
            </div>
        </div>

    </body>

</html>


<?php
    function get_user_data($user, $connect){
        //query sql
        $sql = 'SELECT id, nome, cognome, username, email, password FROM account WHERE username = $1';

       //creo il prepared statement mediante la funzione pg_prepare: 
        //primo parametro --> la risorsa connection al database
        // secondo parametro --> il nome da assegnare al prepared statement
        //terzo parametro --> statement SQL parametrizzato. I parametri devono essere indicati usando i placeholder $1, $2, $3,....
        $prep = pg_prepare($connect, "sqlPassword", $sql);
        if(!$prep){
            echo "Errore nella preparazione: " . pg_last_error($connect);
            return false;
        }

         //eseguo il prepared statement mediante la funzione pg_execute
        //primo parametro --> risorsa Connection al database
        //secondo parametro --> il nome del prepared statement da eseguire
        //terzo parametro --> array di valori da sostituire al placeholder

        $ret = pg_execute($connect , "sqlPassword", array($user));
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
                return $row;
            }

            return false;

        }

    }

?>