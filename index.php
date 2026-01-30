<?php session_start();
    include'db.php';    //connesione al database ($connect);

    $min_data = date('Y-m-d');  //data minima selezionabile (data odierna)

    //Gestione data dinamica
    if(isset($_GET['data_selezionata'])){
        $data_oggi = $_GET['data_selezionata'];             //recupero la data selezionata dall'utente tramite parametro GET
        if($data_oggi < $min_data){
            $data_oggi = $min_data;                         //impongo che la data selezionata non sia antecedente alla data odierna
        }

        $_SESSION['data_film_memorizzata'] = $data_oggi;    //memorizzo la data selezionata in una variabile di sessione
    } 
    
    //se non è stata selezionata nessuna data ma esiste una data memorizzata nella sessione, la uso (utile per navigare tra le pagine senza perdere la data selezionata)
    elseif(isset($_SESSION['data_film_memorizzata'])){
        $data_oggi = $_SESSION['data_film_memorizzata'];   
        if($data_oggi < $min_data){
            $data_oggi = $min_data;                         //impongo che la data selezionata non sia antecedente alla data odierna
            $_SESSION['data_film_memorizzata'] = $data_oggi; //aggiorno la data memorizzata nella sessione
        } 
    }

    //comportamento di default (per il primo accesso alla pagina)
    else {
        $data_oggi = $min_data;  //data del giorno corrente di default
    }
?>
<html lang="it">
   <head>
        <title>MyCinema</title>
        <meta charset="utf-8" />

        <script type="text/javascript" src="script_index.js" defer ></script>
        <link rel="stylesheet" type="text/css" href="style_index.css?v=1.2" />

    </head>
 
    <body>
        <!--Header che mostra una barra di navigazione orizzontale con pulsanti di autenticazione (login e registrazione)--> 
        <header>
            <!--Utilizzo il tag nav per realizzare una barra di navigazione orizzontale -->
            <nav>
                <ul class="horizontal-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="abbonamenti.php">Abbonamenti</a></li>
                    <li><a href="bar.php">Bar</a></li>
                    <?php 
                        if(isset($_SESSION['username'])){
                            echo "<span class=\"user-status\">Benvenuto,&nbsp;<strong>" . $_SESSION['username'] . "</strong></span>";
                            echo "<li><a href=\"profilo.php\" class=\"btn-profile\">Profilo</a></li>";
                            echo "<li><a href=\"logout.php\" class=\"btn-logout\">Logout</a></li>";
                        }else{
                            echo "<li class=\"auth-group\"><a href=\"login.php\" id=\"login\">Accedi</a></li>";
                            echo "<li><a href=\"registrazione.php\" id=\"reg\">Registrati</a></li>";
                            
                        }
                    ?>
                   
                </ul>
            </nav>
        </header>

        <div class="container">
            <!--Utilizzo il tag main per specificare il contenuto principale della pagina-->
            <main class="content">
                <div class="date-selector">
                    <h1 style="margin-right: 40px; margin-bottom: 0;">Film in Programmazione per il <span style="color: #ff9d00;"><?php echo date('d/m/Y', strtotime($data_oggi)); ?></span></h1>
                    <form method="GET" action="index.php" style="margin:0;">
                        <lable style="color: #ccc; margin-right: 10px;">Seleziona Data:</lable>
                        <!-- ricarica la pagina index.php passando la data selezionata come parametro GET 'data_selezionata' -->
                        <input type="date" name="data_selezionata" value="<?php echo $data_oggi; ?>" min="<?php echo date('Y-m-d'); ?>" onchange="this.form.submit()" />
                    </form> 
                </div> 

                <?php
                //query per selezionare i film in programmazione oggi
                $query_film = "SELECT DISTINCT f.id, f.titolo, f.descrizione, f.durata_minuti, f.genere, f.regista, f.attori, f.locandina
                               FROM proiezioni p
                               JOIN films f ON p.film_id = f.id
                               WHERE DATE(p.data_orario) = $1";

                //preparazione della query passando la data di oggi come parametro
                $result_film = pg_query_params($connect, $query_film, array($data_oggi));

                //controllo se ci sono film in programmazione per la data selezionata
                if(pg_num_rows($result_film) == 0){
                    echo "<div style='text-align-:center; padding: 50px; color: #666;'>
                            <h2>🚫 Nessuno spettacolo previsto per questa data.</h2>
                            <p>Ti invitiamo a selezionare un'altra data.</p>
                          </div>";
                }

                //Ciclo per visualizzare tutti i film in programmazione oggi
                while($film = pg_fetch_assoc($result_film)){
                    $film_id = $film['id'];
                ?>

                <!--Sezione per visualizzare le informazioni dei film-->
                <div class="film_container" style="margin-bottom: 40px; border-bottom: 1px solid #333; padding-bottom: 20px;">
                    <div class="info-film">

                        <!-- Mostro la locandina del film -->
                        <?php if(!empty($film['locandina'])): ?> 
                            <img src="img/<?php echo $film['locandina']; ?>" alt="Locandina" style="width: 150px; float: left; margin-right: 20px; border-radius: 5px;"/>
                        <?php endif; ?> 
                        
                        <!-- Mostro il titolo del film -->
                        <h1 style="color: #ff9d00;"><?php echo $film['titolo']; ?></h1>
                        
                        <!-- Mostro il genere e la descrizione del film -->
                        <p id="description">
                            <strong>Genere:</strong> <?php echo $film['genere']; ?><br>
                            <?php echo $film['descrizione']; ?>
                        </p>

                        <!-- Mostro le informazioni aggiuntive del film: regista, cast e durata -->
                        <div class="meta-info" style="clear: both;">
                            <p>Regista: <span><?php echo $film['regista']; ?></span></p>
                            <p>Cast:  <span><?php echo $film['attori']; ?></span></p>
                            <p>Durata: <span><?php echo $film['durata_minuti']; ?> min</span></p>
                        </div>
                    

                    <!--Ottengo gli orari delle proiezioni del film-->
                    <div class="orari-section" style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
                        <?php
                        $query_orari="SELECT p.id, p.data_orario, s.nome AS nome_sala, p.prezzo
                                     FROM proiezioni p
                                     JOIN sale s ON p.sala_id = s.id
                                     WHERE p.film_id = $1 AND DATE(p.data_orario) = $2
                                     ORDER BY p.data_orario ASC";
                        $result_orari = pg_query_params($connect, $query_orari, array($film_id, $data_oggi));

                        while($orario = pg_fetch_assoc($result_orari)){
                            //formattazione dell'orario per prendere solo ore e minuti
                            $orario_inizio = date("H:i", strtotime($orario['data_orario']));
                        ?>
                            <!--Card per visualizzare orario, sala e prezzo del film-->
                        <a href="prenotazione.php?id=<?php echo $orario['id']; ?>" style="text-decoration: none;">
                            <div class = "time-card">
                                <div id="hours"><?php echo $orario_inizio; ?></div>
                                <div id="hall"><?php echo $orario['nome_sala']; ?></div>
                                <div id="price"><?php echo $orario['prezzo']; ?></div>
                            </div>
                        </a>
                        <?php } //fine ciclo orari ?>
                    </div> <!--fine orari-section-->
                
                </div> <?php }  //fine ciclo film ?>
            </main>

        </div>

        <!-- Footer -->
        <footer>
        <div class="footer-left">
            <a href="mailto:info@mycinema.it" class="email-link">
                ✉️ Scrivici un'email
            </a>
        </div>

        <div class="footer-center">
            <p>© 2026 MyCinema</p>
        </div>

        <div class="footer-right">
            <a href="https://maps.app.goo.gl/V7GTsDe5EcUtwLak9" target="_blank" class="map-link">
                📍 Dove siamo
            </a>
        </div>
        </footer>

    </body>
</html>