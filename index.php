<?php session_start();
    include'db.php';    //connesione al database ($connect);

    //Gestione data dinamica
    if(isset($_GET['data_selezionata'])){
        $data_oggi = $_GET['data_selezionata'];
    } else {
        $data_oggi = '2026-01-26';  //data fissa per test (26 gennaio 2026)
    }
?>
<html lang="it">
   <head>
        <title>MyCinema</title>
        <meta charset="utf-8" />

        <style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }

            body{ 
                display: flex;   /*Visualizza l'elemento come un contenitore "flessibile" */
                flex-direction: column;   /*gli elementi della pagina sono disposti uno sotto l'altro (header, content e footer)*/
                background-color: black;

            }

            header{
                background-color: black;
                display: flex;
                align-items: center;
                padding: 15px;
                width: 100%;
            }

            nav{
                width: 100%;

            }

            .horizontal-nav{
                list-style-type: none;
                margin: 0;
                padding: 0 20px;
                background:  #b9713aff;
                width: 100%;  /*allunga la barra su tutta la larghezza*/
                display: flex; /*rende la barra orizzontale*/
                justify-content: center; /*centra la barra*/
                border-radius: 5px; 
            }

  
            /*stile per le voci della barra */
            .horizontal-nav li a{
                display: block;
                color: #000000ff;
                text-align: center;
                font-weight: 060;
                text-decoration: underline;
                font-family: Arial, sans-serif;
                padding: 20px;
            }

            .horizontal-nav li:hover{
                background-color: #c09673ff;
                border-radius: 5px; 
            }

            .auth-group{
                margin-left: auto; /*auto spinge questo elemento e i successivi a destra*/

            }

            .user-status{
                margin-left:auto;
                color: #fff;
                display:flex;
                align-items: center;
                padding: 15px;
                margin-right: 10px;


            }   

            .user-status strong{
                align-items: center;
                color: black;

            }

            .btn-profile{
                color: white;
                padding: 15px;

            }

            .btn-logout{
                color: #862020ff !important; /*Rosso per il logout*/
                font-weight: bold;

            }



            /*Sezione principale (menù laterale + contenuto)*/
            .container{
                display: flex;
                flex: 1;        /*Occupa lo spazio rimanente*/

            }


            .content{
                display: flex;
                flex-direction: column;  /*elementi all'interno dell'elemento di classe 'content' impilati uno sopra l'altro*/
                gap: 30px;
                margin-bottom: 40px;
                padding: 20px;

            }

            .date-selector{
                background-color: #111;
                padding: 20px;
                border-bottom: 1px solid #333;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .date-selector h1 {margin:0; font-size: 24px; color: white; }
            .date-selector input[type="date"] {
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #ff9d00;
                background-color: #222;
                color: white;
                cursor: pointer;
            }

            .info-film {
                padding: 15px;

            }


            h1{
                color: white;
                font-size: 20px;
                margin-bottom: 10px;
                letter-spacing: 1px;

            }

            #description{
                color: #ccc;
                line-height: 1.5;
                margin: 20px 0;
                font-size: 15px;

            }

            .meta-info p{
                color:white;
                margin-bottom: 8px;
                display: flex;
                font-size: 15px;


            }

            p span{
                color: gray;

            }


            /*Stile per la card con informazioni su orari e sala*/
            .time-card{
                /*Colore di sfondo: la funzione linear-gradient imposta un gradiente lineare come colore di sfondo; un gradiente lineare
                  è una transizione sfumata tra due o più colori che si sviluppa lungo una linea retta:
                     1) "to bottom" definisce l'orientamento del gradiente: in questo caso il colore inizia dalla parte superiore dell'elemento e 
                        sfuma verso la parte inferiore;
                      2) il colore di inizio #2f3a52 (un blu desaturato) si trova in alto;
                      3) il colore di fine #262f43 (variante più scura del blu) si trova in basso
                      Usando questa funzione la barra di navigazione orizzontale sembra un elemento con una sua profondità che si integra con
                      lo sfondo scuro del sito.*/
                background: linear-gradient(to bottom,#2f3a52,#262f43);
                padding: 15px;
                border-radius: 5px;
                border: 1px solid #333;
                transition: 0.3s;
                cursor: pointer;
                width: 150px;




            }

            #hours{
                font-weight: bold;
                margin-bottom: 5px;
                color: white;

            }

            #hall{
                color: #888;
                margin-bottom: 5px;

            }

            #tech{
                color: #ff9d00;


            }

            #price{
                text-align: right;
                color: #fff;


            }

            /*Stile per il footer*/
            footer{
                color: white;
                background-color: #000;
                text-align: center;
                padding: 15px;
                border-top: 1px solid #333


            }

    </style>

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
                        <input type="date" name="data_selezionata" value="<?php echo $data_oggi; ?>" onchange="this.form.submit()" />
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
            <p> &copy; 2026 MyCinema</p>            
        </footer>

        <script type = "text/javascript">
            var inputElems = document.getElementsByClassName("horizontal-nav");
            for(var i = 0; i < inputElems.length; i++){
                inputElems[i].addEventListener("mouseover", handleMouseOver);
                inputElems[i].addEventListener("mouseout", handleMouseOut);

            }

            function handleMouseOver(e){
                //applico la sottolineatura solo ai link (tag a)
                //si utilizza a tale scopo la proprietà tagName che ritorna il tag name dell'elemento su cui la proprietà
                // è chiamata; la proprietà tagName ritorna una stringa che indica il tag name dell'elemento; ad esempio,
                //se l'elemento ha tag name img, la proprietà ritorna IMG 
                if(e.target.tagName == "A"){
                    e.target.style.textDecoration = "underline";
                }
            }

            function handleMouseOut(e){
                if(e.target.tagName == "A"){    
                    e.target.style.removeProperty("text-decoration");
                }
            }

            //seleziona il primo elemento del DOM che corrisponde al selettore .btn-logout
            var btnLogout = document.querySelector(".btn-logout");

            //verifica che il pulsante Logout esista (se l'utente non è loggato non ci sarà)
            if(btnLogout){
                btnLogout.addEventListener("click", handleClick);
            }

            function handleClick(e){

                //blocca il reindirizzamento automatico a 'logout.php'
                e.preventDefault();

                //recupero il valore dell'attributo href dell'elemento HTML che ha ricevuto l'evento (ossia il tag <a> con href='logout.php')
                var infoUrl = this.href;

                //il cursore diventa una rotellina
                document.body.style.cursor = "wait";

                //feedback visivo (il testo del link cambia da logout a "Chiusura sessione .....")
                this.innerHTML = "Chiusura sessione.... 🎬";
                
                //attesa di 2 secondi prima di ricaricare la pagina
                setTimeout(function(){
                    window.location.href = infoUrl;
                }, 2000);
            }

        </script>
    </body>
</html>