<?php
    include 'db.php';
    session_start();
?>
<html lang="it">
    <head>
        <title>MyCinema : ACCEDI</title>
        <meta charset="utf-8">

        <script type = "text/javascript">
            function validaLogin(nomeModulo){
                if(nomeModulo.username.value == ""){
                    alert("⚠️ Attenzione! È richiesto un nome utente (username).");
                    nomeModulo.username.focus();
                    return false;
                }

                if(nomeModulo.password.value == ""){
                    alert("⚠️ Attenzione! È richiesta una password.");
                    nomeModulo.password.focus();
                    return false;

                }

                return true;

            }



        </script>

        <style>

            body{
                display: flex;   /*Visualizza l'elemento come un contenitore "flessibile" */
                /*gli elementi della pagina sono disposti uno sotto l'altro (header, content e footer)*/
                background-color: black;
                justify-content: center;
                padding: 100px;
            }


            .auth-container{
                display: flex;             /*Visualizza l'elemento come un contenitore "flessibile" */
                justify-content: center;  /*centra il div*/
                align-items: center;      /*allinea gli elementi del div al centro*/
                padding: 20px;
                /*colore di sfondo: la funzione radial-gradient imposta un gradiente radiale come colore di sfondo; un 
                gradiente radiale è un passaggio di colore che si sviluppa in modo circolare partendo da un punto centrale verso l'esterno:
                  
                    1) il valore "circle" definisce la forma (shape) del gradiente: in questo caso forza il gradiente ad avere una forma perfettamente circolare;
                    2) il valore "center" definisce la posizione (position) del gradiente: in questo caso posiziona il punto di inizio esattamente al centro
                      dell'elemento;
                    3) i colori e le percentuali definiscono quali colori usare e dove devono trovarsi nel raggio del cerchio:
                          - #1a1a1a 0%: è il colore di partenza (un grigio molto scuro); lo 0% indica che questo colore si trova esattamente
                          nel punto centrale;
                          - #000 100% : è il colore finale (nero) ; il 100% indica che il nero viene aggiunto al bordo esterno del 
                          contenitore
                    Le "interruzioni" (stops) di colore sono i colori tra cui si desidera ottenere transizioni fluide; questo valore è costituito
                    da un valore di colore, seguito da una o due posizioni di interruzione opzionale (ovvero una percentuale compresa tra 0% e 100% o una lunghezza 
                    lungo l'asse del gradiente).*/
                background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%); /*colore di sfondo*/
            }

            .header{
                text-align: center;
                margin-right: 80px;   /* Crea lo spazio tra MyCinema e il box di login */
                max-width: 300px;     /* Evita che il testo si allarghi troppo */
            }

            h1{
                color: #ff9d00;
                font-size: 40px;
            }

            #header-par{
                color: #b8b7b7ff;
                margin-top: 10px;
            }

            /*Box centrale per l'autenticazione*/
            .auth-box{
                background-color: #111;
                padding: 80px;
                width: 100%;
                border: 1px solid #222;
                border-radius: 10px; 
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
                border-radius: 5px; 
            
            }

            #auth-button{
                background-color: #ff9d00;
                color: black;
                padding: 15px;
                width: 100px;
                font-weight: bold;
                cursor: pointer;
                
            }

            #auth-button:hover{
                background-color: #e68a00;
                transform: scale(1.05); 

            }

            p{
                margin-top: 25px;
                text-align: center;
                color: #b8b7b7ff;

            }

            .returnHome{
                display: flex;
                justify-content: center;
                align-items: center;

            }

            a{
                color: #ff9d00;
                margin-top: 25px;
                font-weight: bold;
                text-decoration: none;
                font-size: 15px;

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

        <script type = "text/javascript">
            //individuo gli elementi che hanno il tag input
            var inputElements = document.getElementsByTagName("input");
            for(var i = 0; i < inputElements.length; i++){
                inputElements[i].onfocus = handleFocusEvent;
                inputElements[i].onblur = handleFocusEvent;

            }

            function handleFocusEvent(e){
                if(e.type == "focus"){
                    e.target.style.border = "thick solid #ff9d00";

                }else{
                    e.target.style.removeProperty("border");

                }
            }

            //individuo gli elementi che anno il tag a
            var inputElems = document.getElementsByTagName("a");

            for(var i = 0; i < inputElems.length; i++){
                inputElems[i].addEventListener("mouseover", handleMouseOver);
                inputElems[i].addEventListener("mouseout", handleMouseOut);
            }

            function handleMouseOver(e){
                e.target.style.textDecoration = "underline";

            }

            function handleMouseOut(e){
                e.target.style.removeProperty("text-decoration");
            }


        </script>
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