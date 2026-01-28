<?php 
    session_start();
    include 'db.php';

    //reindirizza al login se l'utente non è loggato

    //recupero l'ID dell'utente 
    $user_id = $_SESSION['id'];

    //effettuo una query per recuperare gli abbonamenti attivi dell'utente
    //si utilizza un JOIN per prendere il nome del piano dalla tabella 'piani'
    $query_abbonamenti = "SELECT p.nome, a.data_inizio, a.data_fine, a.stato 
                          FROM abbonamenti a
                          JOIN piani p ON a.id_piano = p.id
                          WHERE a.id_utente = $1 AND a.stato = 'attivo'
                          ORDER BY a.data_fine DESC";

    //creo il prepared statement mediante la funzione pg_prepare: 
        //primo parametro --> la risorsa connection al database
        // secondo parametro --> il nome da assegnare al prepared statement
        //terzo parametro --> statement SQL parametrizzato. I parametri devono essere indicati usando i placeholder $1, $2, $3,....
    $res = pg_prepare($connect, "get_subs", $query_abbonamenti);
    
      //eseguo il prepared statement mediante la funzione pg_execute
        //primo parametro --> risorsa Connection al database
        //secondo parametro --> il nome del prepared statement da eseguire
        //terzo parametro --> array di valori da sostituire al placeholder
    $ret = pg_execute($connect, "get_subs", array($user_id));

    //recupero tutte le righe dal risultato della query e le memorizzo in un array;
    $abbonamenti = pg_fetch_all($ret);

    if(!$abbonamenti){
        $abbonamenti = [];
    }
?>

<html lang = "it">
    <head>
        <title>MyCinema : IL MIO PROFILO</title>
       <meta charset = "utf-8" />
       
       <link rel="stylesheet" type="text/css" href="style_profilo.css" />
    </head>

    <body>
        <div class="container">
            <div class="profile-sec">
                <h1>Dati del profilo</h1>
                <!-- visualizzo i dati di un determinato utente !-->
                <div class = "profile-info">
                    <p><span class = "label">Username: </span> <?php echo $_SESSION['username']; ?></p>
                    <p><span class = "label">Nome: </span> <?php echo $_SESSION['nome']; ?></p>
                    <p><span class = "label">Cognome: </span><?php echo $_SESSION['cognome']; ?></p>
                    <p><span class = "label">Email: </span> <?php echo $_SESSION['email']; ?></p> 
                </div>

                <!-- visualizzo gli abbonamenti attivi per un dato utente !-->
                <div class="subs-sec">
                    <h2> I tuoi Abbonamenti</h2>
                    <?php if(count($abbonamenti) > 0): ?>
                        <?php foreach ($abbonamenti as $sub): ?>
                            <div class="sub-card">
                                <span class = "sub_name"><?php echo htmlspecialchars($sub['nome']); ?></span>
                                <div class="sub_dates">
                                    <!-- funzione date per trasformare il formato del database(YYYY-MM-DD) in quello italiano !-->
                                    Scadenza: <strong><?php echo date('d/m/Y', strtotime($sub['data_fine'])); ?> </strong>
                            </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-subs">Non hai abbonamenti attivi al momento.</p>
                    <?php endif; ?>        
                </div>

                <a href="index.php">&larr; Torna alla Home</a>
            </div>
        </div>

        <script type = "text/javascript">
            //individuo gli elementi che hanno il tag a
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