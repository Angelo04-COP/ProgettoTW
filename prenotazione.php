<?php
    session_start();
    include 'db.php';   //connesione al database

    // --- BLOCCO DI GESTIONE AGGIUNTA PRENOTAZIONE AL CARRELLO (per non creare altre pagine dinamiche) ---
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['posti_scelti'])) {

        //controllo se l'utente è loggato (tecnicamente non dovrebbe arrivare qui se non loggato)
        if(!isset($_SESSION['id'])) {
            header("Location: login.php");
            exit();
        }

        //recupero dati dal form
        $proiezione_id = $_POST['proiezione_id'];
        $posti_string = $_POST['posti_scelti'];   //stringa con posti separati da virgola "A-1,B-2,C-3"

        if(!empty($posti_string)) {
            //recupero informazioni sulla proiezione dal DB
            $sql="SELECT f.titolo, p.data_orario, p.prezzo
                  FROM proiezioni p
                  JOIN films f ON p.film_id = f.id
                  WHERE p.id = $1";
            $res = pg_query_params($connect, $sql, array($proiezione_id));
            $info_film = pg_fetch_assoc($res);

            if($info_film) {
                //trasformo la stringa dei posti in un array
                $posti_array = explode(',', $posti_string);     //spezza la stringa in array quando trova la virgola 

                foreach($posti_array as $posto) {
                    //per ogni posto divido in fila e numero e li metto in 2 arrays separati
                    list($fila, $numero) = explode('-', $posto);    //spezza il singolo posto (es. A-5) in fila (A) e numero (5)

                    //Controllo per evitare duplicati nel carrello
                    $gia_presente = false;
                    if (isset($_SESSION['carrello'])) {
                        foreach ($_SESSION['carrello'] as $item) {
                            //in caso nel carrello c'è gia un biglietto identico fermo l'aggiunta
                            if (isset($item['tipo_item']) && $item['tipo_item'] == 'biglietto' &&
                                $item['id'] == $proiezione_id &&
                                $item['fila'] == $fila &&
                                $item['numero'] == $numero) {
                                $gia_presente = true;
                                break;
                            }
                        }
                    }

                    //se non è già presente nel carrello il biglietto, lo aggiungo
                    if (!$gia_presente) {
                        $biglietto = [
                            'tipo_item' => 'biglietto',
                            'id' => $proiezione_id,
                            'nome' => "🎟️ " . $info_film['titolo'] . " (" . date("d/m/Y H:i", strtotime($info_film['data_orario'])) . ")- Posto $fila-$numero",
                            'prezzo' => $info_film['prezzo'],
                            'fila' => $fila,
                            'numero' => $numero
                        ];

                        //aggiungo il biglietto al carrello (sessione)
                        $_SESSION['carrello'][] = $biglietto;       //aggiunge l'array (biglietto) in fondo all'array carrello
                    }
                }
                
                //reindirizzo l'utente al carrello
                header("Location: carrello.php");
                exit();
            }
        }
    }
    // --- FINE BLOCCO GESTIONE AGGIUNTA PRENOTAZIONE AL CARRELLO ---

    //Controllo id proiezione dal URL
    if(!isset($_GET['id'])) {
        die("Errore: Nessuna proiezione selezionata.");
    }
    $id_proiezione = $_GET['id'];

    //Recupero informazioni su Film e Sala dal Database
    $sql_info = "SELECT f.titolo, f.locandina, s.nome AS nome_sala, s.capienza_totale, p.data_orario, p.prezzo
                 FROM proiezioni p
                 JOIN films f ON p.film_id= f.id
                 JOIN sale s ON p.sala_id = s.id
                 WHERE p.id = $1";

    $res_info = pg_query_params($connect, $sql_info, array($id_proiezione));
    $info = pg_fetch_assoc($res_info);

    //Controllo se il recupero delle informazioni è andato a buon fine
    if(!$info) {
        die("Errore: Proiezione non trovata nel database.");
    }

    //Recupero posti già prenotati per la proiezione (serve per visualizzare la mappa posti)
    $sql_occupati = "SELECT fila, numero FROM prenotazioni WHERE proiezione_id = $1";
    $res_occupati = pg_query_params($connect, $sql_occupati, array($id_proiezione));

    //creo un formato stringa per i posti occupati
    $posti_occupati = [];
    while ($row = pg_fetch_assoc($res_occupati)) {
        $posti_occupati[] = $row["fila"] . "-" . $row["numero"];
    }

    //Recupero eventuali posti gia selezionati nel carrello
    $posti_nel_carrello = [];
    if (isset($_SESSION['carrello'])) {
        foreach ($_SESSION['carrello'] as $item) {
            //scorro il carrello, se trovo biglietti per questa proiezione li aggiungo all'array
            if (isset($item['tipo_item']) && $item['tipo_item'] == 'biglietto' && $item['id'] == $id_proiezione) {
                //costruisco la stringa del posto (es. A-5)
                $posti_nel_carrello[] = $item['fila'] . "-" . $item['numero'];
            }
        }
    }

    //verifico se l'utente è loggato (controllo se c'è l'ID nella sessione)
    $is_logged = isset($_SESSION['id']) ? true : false;
?>

<!-- Qui inizia il codice HTML per la pagina di prenotazione -->
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Prenota - <?php echo $info['titolo']; ?></title>
    <meta charset="UTF-8">
    <style>
        /*Stile CSS per questa pagina (da spostare in un file esterno se necessario)*/
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .container {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            width: 100%;
        }

        /*colonna sinistra - info film */
        .info-box {flex: 1;}
        .info-box img {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        h1 {color: #ff9d00; margin-bottom: 5px;}
        .dettagli {color: #ccc; font-size: 1.1em; line-height: 1.6;}

        /*colonna centrale - mappa posti */
        .map-box { 
            flex: 2; 
            background: #111;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #333;
            text-align: center;
        }
        .screen {
            background: #555;
            height: 10px;
            margin: 0 auto 30px auto;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .screen-text {font-size: 12px; color: #888; margin-bottom: 20px;}

        /*Griglia posti*/
        .seats-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        .row { display: flex; gap: 8px; justify-content: center; }

        /*il singolo posto*/
        .seat {
            width: 30px;
            height: 30px;
            background: #444;       /*Posto libero (Grigio) */
            border-radius: 5px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
        }

        /*Stati del posto*/
        .seat:hover {background: #666;}     /*Hover sul posto libero*/
        .seat.selected {
            background-color: #28a745;        /*Posto selezionato (Verde)*/
            color: black;
            font-weight: bold;
            box-shadow: 0 0 10px #28a745;
        } 
        .seat.occupied {
            background-color: #aa0000;        /*Posto occupato (Rosso)*/
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        /*Riepilogo prenotazione (a destra)*/
        .summary{
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: right;
        }
        #total-price {
            font-size: 1.5em;
            font-weight: bold;
            color: #ff9d00;
        }

        .btn-prenota {
            background-color: #ff9d00;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-prenota:disabled {background-color: #555; cursor: not-allowed; color: #888;}

        .btn-login{
            background-color: #2f3a52;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        a{ color: #ff9d00; text-decoration: none; }
    </style>
</head>

<!-- Inizio del corpo della pagina -->
<body>
    <div class="container">
        <!--Colonna sinistra: Info Film-->
        <div class="info-box">
            <?php if(!empty($info['locandina'])) : ?>
                <img src="img/<?php echo $info['locandina']; ?>" alt="Locandina di <?php echo $info['titolo']; ?>">
            <?php endif; ?>

            <h1><?php echo $info['titolo']; ?></h1>

            <p class="dettagli">
                <strong>Sala:</strong> <?php echo $info['nome_sala']; ?><br>
                <strong>Data e Ora:</strong> <?php echo date("d/m/Y H:i", strtotime($info['data_orario'])); ?><br>
                <strong>Prezzo Biglietto:</strong> <?php echo $info['prezzo']; ?> €
            </p>

            <!--Avviso in caso di utente non loggato-->
            <?php if(!$is_logged): ?>
                <div style="background: #221111; padding: 15px; border: 1px solid #aa4444; border-radius: 5px; margin-top: 20px;">
                    <p style="color: #ffaaaa; margin: 0; font-weight: bold;"> 🔒 Login Richiesto </p> 
                    <p style="font-size: 14px; margin: 5px 0 0 0;">Per selezionare i posti devi essere registrato!</p>
                    <a href="login.php" class="btn-login">Accedi / Registrati</a>
                </div>
            <?php endif; ?>

            <!--Link per tornare alla home-->
            <br>
            <a href="index.php">&larr; Torna alla Home</a>
        </div>

        <!--Colonna centrale/destra: Mappa Posti-->
        <div class="map-box">
            <!--Schermo-->
            <div class="screen"></div>
            <p class="screen-text">Schermo</p>

            <!--Griglia Posti (Centrale)-->
            <div class="seats-grid">
                <?php
                    //Griglia di posti: file con lettere (ogni fila ha 15 posti)
                    $posti_per_fila = 15;
                    $totale_posti = $info['capienza_totale'];      //si costruisce la grigia dinamicamente in base alla capienza della sala
                    $file_totali = ceil($totale_posti / $posti_per_fila);   //calcolo il numero di file necessarie arrotondando per eccesso
                    $alfabeto = range('A', 'Z');   //array con lettere A-Z per le file

                    //Ciclo per creare le file(A, B, C, ...)
                    for ($i = 0; $i < $file_totali; $i++) {
                        //Caso limite: se finiscono le lettere uso sempre la Z0 - Z1 - Z2 ...
                        $nome_fila = isset($alfabeto[$i]) ? $alfabeto[$i] : 'Z'.$i;

                        //Ciclo per creare i posti in ogni fila (1, 2, 3, ...)
                        echo '<div class="row">';
                        for ($j = 1; $j <= $posti_per_fila; $j++) {
                            //controllo di non superare il totale posti (utile per l'ultima fila non completa)
                            if (($i * $posti_per_fila + $j) > $totale_posti) break;
                            $id_posto = $nome_fila . '-' . $j;     //es. A-1, A-2, B-1, B-2, ...
                            
                            //Controllo se il posto è occupato (presente nell'array dei posti occupati nel DB)
                            //in caso di occupato aggiungo la classe 'occupied' (CSS lo rende rosso e non cliccabile)
                            //in caso di libero non aggiungo nulla (CSS lo rende grigio e cliccabile)
                            //in caso già selezionato nel carrello aggiungo la classe 'selected' (CSS lo rende verde)
                            $classe_stato = '';
                            if (in_array($id_posto, $posti_occupati)) {
                                $classe_stato = 'occupied';
                            } elseif (in_array($id_posto, $posti_nel_carrello)) {
                                $classe_stato = 'selected';
                            }

                            //Stampo il posto in HTML
                            //uso attributi coustum data-fila e data-numero per memorizzare info del posto e funzione JS onclick
                            //quando clicco sul posto viene chiamata la funzione JS 'selezionaPosto(this)' passando l'elemento cliccato
                            //la funzione JS gestisce la selezione/deselezione del posto
                            echo "<div class='seat $classe_stato' data-fila = '$nome_fila' data-numero = '$j' onclick='selezionaPosto(this)'> 
                                        $nome_fila$j
                                    </div>";  
                        }
                        echo '</div>'; //fine riga
                    } //fine griglia posti
                ?>
            </div> <!--fine seats-grid-->

            <!--Riepilogo Ordine e bottone prenota (Destra)-->
            <div class = "summary">
                <p>Posti Selezionati: <span id="count">0</span></p>
                <p>Prezzo Totale: <span id="total-price">0.00</span> €</p>

                <!--Bottone Prenota (disabilitato se non loggato)-->
                <!--preparazione della form per inviare i dati della prenotazione al carrello-->
                <form id="booking-form" action="" method="POST">
                    <input type="hidden" name="proiezione_id" value="<?php echo $id_proiezione; ?>">
                    <input type="hidden" name="posti_scelti" id="input-posti">

                    <?php if($is_logged): ?>
                        <button type="submit" class="btn-prenota" id="btn-prenota" disabled>CONFERMA ACQUISTO</button>
                    <?php else: ?>
                        <button type="button" class="btn-prenota" disabled style="opacity: 0.5;">EFFETTUA IL LOGIN PER PRENOTARE</button>
                    <?php endif; ?>
                </form>
            </div> <!--fine summary-->
        </div> <!--fine map-box-->
    </div> <!--fine container-->
    
    <!--Script JS per gestire la selezione dei posti-->
    <script>
        //recupero dati php per uso in JS
        const prezzoBiglietto = <?php echo $info['prezzo']; ?>;
        const isLogged = <?php echo $is_logged ?>;

        //array per tenere traccia dei posti selezionati (stato iniziale: posti già selezionati nel carrello)
        let postiSelezionati = <?php echo json_encode($posti_nel_carrello); ?>;     //trasforma l'array PHP in array JS

        //Funzione chiamata quando si clicca su un posto
        function selezionaPosto(elemento) {
            //Controllo LOGIN: se non loggato avviso e fermo tutto
            if(!isLogged) {
                alert("⚠️ Per selezionare i posti devi prima effettuare l'accesso!");
                return;
            }

            //Controllo se il posto è occupato (rosso) - ignoro il click
            if(elemento.classList.contains('occupied')) {
                return;
            }

            //Recupero info del posto dai data-attributes (hidden in HTML)
            const fila = elemento.getAttribute('data-fila');
            const numero = elemento.getAttribute('data-numero');
            const idPosto = fila + '-' + numero;

            //Gestisco differenza tra selezione e deselezione
            if(elemento.classList.contains('selected')) {
                //Gestisco la deselezione del posto (verde -> grigio)
                elemento.classList.remove('selected');
                postiSelezionati = postiSelezionati.filter(posto => posto !== idPosto);     //filtro l'array per rimuovere il posto
            } else {
                //Gestisco la selezione del posto (grigio -> verde)
                elemento.classList.add('selected');
                postiSelezionati.push(idPosto);    //aggiungo il posto all'array
            }

            //Aggiorno il riepilogo (conteggio posti e prezzo totale)
            aggiornaTotali();
        }

        //Funzione per aggiornare il riepilogo dei posti selezionati e il prezzo totale
        function aggiornaTotali() {
            //Aggiorno il conteggio posti (si vede nell'HTML)
            document.getElementById('count').innerText = postiSelezionati.length;

            //Calcolo e aggiorno il prezzo totale
            let totale = postiSelezionati.length * prezzoBiglietto;
            document.getElementById('total-price').innerText = totale.toFixed(2);  //due decimali

            //Abilito il tasto prenota se c'è almeno un posto selezionato
            const btn = document.getElementById('btn-prenota');
            if(btn) {
                btn.disabled = postiSelezionati.length === 0;
            }

            //Aggiorno l'input hidden con i posti selezionati (per invio form)
            //i posti sono inviati come stringa separata da virgole "A-1,B-2,C-3"
            document.getElementById('input-posti').value = postiSelezionati.join(',');
        }

        aggiornaTotali();  //chiamo la funzione all'inizio per calcolare subito il totale di eventuali posti già selezionati nel carrello
    </script>
</body>

</html>

