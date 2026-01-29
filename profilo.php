<?php 
    session_start();
    include 'db.php';

    //reindirizza al login se l'utente non è loggato
    if(!isset($_SESSION['id'])){
        header("Location: login.php");
        exit();
    }
    //recupero l'ID dell'utente 
    $user_id = $_SESSION['id'];

    //effettuo una query per recuperare gli abbonamenti attivi dell'utente, quelli scaduti verranno automaticamente nascosti
    //si selezionano il nome del piano, la data di inizio e la data di fine:
    // - FROM indica la tabella principale da cui partire;
    // - a partire dall'id del piano nell'abbonamento, si trova la riga corrispodndente nella tabella dei piani;
    // - "WHERE a.id_utente = $1" è un filtro per l'utente loggato;
    // - "AND a.data_fine >= CURRENT_DATE" è un filtro "automatico": il database confronta la data di scadenza salvata con la data odierna, restituita da CURRENT_DATE 
    //Se la scadenza è passata (anche solo da un giorno), quella riga viene esclusa dai risultati.
    // - "ORDER BY a.data_fine DESC" ordina i risultati in modo decrescente: l'abbonamento con la scadenza più lontana nel futuro appare per primo nella lista
    $query_abbonamenti = "SELECT p.nome, a.data_inizio, a.data_fine 
                          FROM abbonamenti a
                          JOIN piani p ON a.id_piano = p.id
                          WHERE a.id_utente = $1
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

    //creo un array per gli abbonamenti attivi e un array per gli abbonamenti scaduti
    $abbonamenti_attivi = [];
    $abbonamenti_scaduti = [];
    foreach($abbonamenti as $sub){
        //se un abbonamento non è scaduto, lo aggiungo all'array di abbonamenti attivi
        //controllo temporale: se la data di fine dell'abbonamento è maggiore di "adesso", allora è attivo
        if(strtotime($sub['data_fine']) >= time()){
            $abbonamenti_attivi[] = $sub;      
        }else{
            //altrimenti lo aggiungo all'array di abbonamenti scaduti
            $abbonamenti_scaduti[] = $sub;
        }
    }

    //effettuo una query per recuperare i biglietti attivi dell'utente
    //si utilizza un JOIN per unire le tabelle films, sale, proiezioni e prenotazioni
    //si parte dalla tabella prenotazioni e poi:
    //- si collega il biglietto alla specifica proiezione (primo JOIN)
    //- dalla proiezione risale alla tabella dei film per scoprire come si chiama il film (secondo JOIN)
    //- dalla proiezione si risale alla tabella delle sale per sapere in quale sala si deve andare (terzo JOIN)
    //Con "WHERE pre.id_utente = $1" si sta dicendo al database di prendere solo i biglietti che appartengono all'utente
    // loggato: il placeholder $1 verrà sostituito da $_SESSION['id];
    // - "AND p.data_orario >= NOW()" è un filtro automatico: il database il timestamp del film (cioè Data + Ora, Minuti e Secondi ) 
    // con il timestamp odierno, restituita dalla funzione NOW() 
    //Se la scadenza è passata, quella riga viene esclusa dai risultati.
    // Con "ORDER BY p.data_orario ASC" si ordinano i biglietti in ordine cronologico
    $query_biglietti = "SELECT f.titolo, s.nome AS sala, p.data_orario, pre.fila, pre.numero
        FROM prenotazioni pre
        JOIN proiezioni p ON pre.proiezione_id = p.id
        JOIN films f ON p.film_id = f.id
        JOIN sale s ON p.sala_id = s.id
        WHERE pre.utente_id = $1
        ORDER BY p.data_orario ASC";

//creo il prepared statement mediante la funzione pg_prepare: 
        //primo parametro --> la risorsa connection al database
        // secondo parametro --> il nome da assegnare al prepared statement
        //terzo parametro --> statement SQL parametrizzato. I parametri devono essere indicati usando i placeholder $1, $2, $3,....
    $res_biglietti = pg_prepare($connect, "get_tickets", $query_biglietti);

    
    //eseguo il prepared statement mediante la funzione pg_execute
        //primo parametro --> risorsa Connection al database
        //secondo parametro --> il nome del prepared statement da eseguire
        //terzo parametro --> array di valori da sostituire al placeholder
    $ret_biglietti = pg_execute($connect, "get_tickets", array($user_id));


     //recupero tutte le righe dal risultato della query e le memorizzo in un array;
    $biglietti = pg_fetch_all($ret_biglietti);

    if(!$biglietti){
        $biglietti = [];
    }

    //creo un array per i biglietti attivi e un array per i biglietti scaduti
    $biglietti_attivi = [];
    $biglietti_scaduti = [];
    foreach($biglietti as $t){
        //se un biglietto non è scaduto, lo aggiungo all'array di biglietti attivi
        //controllo temporale: se la data di validità del biglietto è maggiore di "adesso", allora è attivo
        if(strtotime($t['data_orario']) >= time()){
            $biglietti_attivi[] = $t;      
        }else{
            //altrimenti lo aggiungo all'array di biglietti scaduti
            $biglietti_scaduti[] = $t;
        }
    }


?>

<html lang = "it">
    <head>
        <title>MyCinema : IL MIO PROFILO</title>
       <meta charset = "utf-8" />
       
       <script type="text/javascript" src="script_profilo.js" defer></script>
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
                        <?php foreach ($abbonamenti_attivi as $sub): 
                            //controllo temportale: se la data di fine è maggiore di "adesso" è attivo
                            //$is_sub_active = strtotime($sub['data_fine']) > time();
                        ?>
                            <div class="sub-card">
                                <span class = "sub_name"><?php echo htmlspecialchars($sub['nome']); ?></span>
                                <div class="sub_dates">
                                    <!-- funzione date per trasformare il formato del database(YYYY-MM-DD) in quello italiano !-->
                                    Scadenza: <strong><?php echo date('d/m/Y', strtotime($sub['data_fine'])); ?> </strong>
                                </div>

                            <div class="sub-status">
                                <span class="status-badge status-active">Attivo</span>
                                <?php   /* <?php if($is_sub_active): ?>
                                        <span class="status-badge status-active">Attivo</span>
                                    <?php else: ?>
                                        <span class = "status-badge status-expired">Scaduto</span>
                                    <?php endif; ?>   
                                 */ ?>   
                             </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-subs">Non hai abbonamenti attivi al momento.</p>
                    <?php endif; ?> 
                    
                    <?php if(count($abbonamenti_scaduti) > 0): ?>
                        <button class="toggle-history" onclick="toggleVisibility('history-subs', event)">▼ Mostra Storico Abbonamenti</button>
                        <div id="history-subs" class="history-content">
                            <?php foreach($abbonamenti_scaduti as $sub): ?>
                                <div class="sub-card history-card">
                                    <span class="sub_name"><?php echo htmlspecialchars($sub['nome']); ?></span>
                                    <div class="sub_dates">
                                        Terminato il: <?php echo date('d/m/Y', strtotime($sub['data_fine'])); ?>

                                    </div>

                                    <div class="sub-status">
                                        <span class="status-badge status-expired">Scaduto</span>
                                    </div>
                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>    
                 
                <!-- visualizzo i biglietti attivi per un dato utente !-->
                <div class="tickets-sec">
                    <h2>I tuoi Biglietti</h2>
                    <?php if(count($biglietti) > 0): ?>
                        <?php foreach($biglietti_attivi as $t):
                            //si verifica se il film è ancora da proiettare: si confronta la data del film (trasformata in un TimeStamp
                            // Unix, un numero che rappresenta i secondi trascorsi dal 1 gennaio 1970) con il momento esatto attuale (utilizzando time())
                            //$is_ticket_active = strtotime($t['data_orario']) > time();
                        ?>

                            <div class = "ticket-card">
                                <div class = "ticket-info">
                                    <h4><?php echo htmlspecialchars($t['titolo']); ?></h4>
                                    <div class="ticket-details">
                                        <strong>Sala:</strong> <?php echo htmlspecialchars($t['sala']); ?><br>
                                        <!-- funzione date per trasformare il formato del database(YYYY-MM-DD H:i) in quello italiano !-->
                                        <strong>Data:</strong> <?php echo date("d/m/Y H:i", strtotime($t['data_orario'])); ?><br>
                                        <strong>Posto:</strong> Fila <?php echo $t['fila']; ?>, Numero <?php echo $t['numero']; ?>
                                    </div>
                                </div>

                            <div class="ticket-status">
                                <span class = "status-badge status-active">Valido</span>
                               <?php /*<?php if($is_ticket_active): ?>
                                    <span class = "status-badge status-active">Valido</span>
                                <?php else: ?>
                                    <span class="status-badge status-expired">Scaduto</span>    
                                <?php endif; ?>
                                */ ?>
                            </div>   
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-subs">Non hai biglietti per i prossimi film.</p>
                    <?php endif; ?>

                    <?php if(count($biglietti_scaduti) > 0): ?>
                        <button class="toggle-history" onclick="toggleVisibility('history-tickets', event)">▼ Mostra Storico Biglietti</button>
                        <div id="history-tickets" class="history-content">
                            <?php foreach($biglietti_scaduti as $t): ?>
                                <div class="ticket-card history-card">
                                    <div class = "ticket-info">
                                        <h4><?php echo htmlspecialchars($t['titolo']); ?></h4>
                                        <div class="ticket-details">
                                            Visto il: <?php echo date("d/m/Y", strtotime($t['data_orario'])); ?>
                                        </div>
                                    </div>
                                    <div class="ticket-status">
                                        <span class = "status-badge status-expired">Scaduto</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>    
                <a href="index.php">&larr; Torna alla Home</a>
            </div>
        </div>

</body>


</html>