<?php session_start() ?>
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
                /*Colore di sfondo: la funzione linear-gradient imposta un gradiente lineare come colore di sfondo; un gradiente lineare
                  è una transizione sfumata tra due o più colori che si sviluppa lungo una linea retta:
                     1) "to bottom" definisce l'orientamento del gradiente: in questo caso il colore inizia dalla parte superiore dell'elemento e 
                        sfuma verso la parte inferiore;
                      2) il colore di inizio #2f3a52 (un blu desaturato) si trova in alto;
                      3) il colore di fine #262f43 (variante più scura del blu) si trova in basso
                      Usando questa funzione la barra di navigazione orizzontale sembra un elemento con una sua profondità che si integra con
                      lo sfondo scuro del sito.*/
                background: linear-gradient(to bottom,#2f3a52,#262f43);
                width: 100%;  /*allunga la barra su tutta la larghezza*/
                display: flex; /*rende la barra orizzontale*/
                justify-content: center; /*centra la barra*/
                

            }



            /*stile per le voci della barra */
            .horizontal-nav li a{
                display: block;
                color: white;
                text-align: center;
                text-decoration: none;
                font-family: Arial, sans-serif;
                padding: 20px;

            }

            .horizontal-nav li:hover{
                background-color: #333;

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
                color: #ff9d00;

            }

            .btn-profile{
                color: white;
                padding: 15px;

            }

            .btn-logout{
                color: #ff4444 !important; /*Rosso per il logout*/
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
                    <li><a href="#">Programmazione</a></li>
                    <li><a href="#">Abbonamenti</a></li>
                    <li><a href="#">Offerte</a></li>
                    <?php 
                        if(isset($_SESSION['username'])){
                            echo "<span class=\"user-status\">Benvenuto , <strong>" . $_SESSION['username'] . "</strong></span>";
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
                <h1>Oggi al Cinema</h1>
                <div class="info-film">
                    <h1>AVATAR: FUOCO E CENERE</h1>
                    <p id="description">
                        "Avatar: Fuoco e Cenere", il terzo film del fenomenale successo della saga di 
                        "Avatar", viene presentato a Dicembre 2025, in <br> tutto il mondo, esclusivamente al cinema.
                        James Cameron riporta gli spettatori a Pandora in una nuova coinvolgente <br> avventura 
                        con il Marine, ora diventato leader dei Na'vi, Jake Sully (Sam Worthington), la guerriera Na'vi, Neytiri (Zoe Saldaña), <br>
                        e tutta la famiglia Sully.
                    </p>

                    <div class="meta-info">
                        <p id="cast">Cast:  <span>Zoe Saldana, Sam Worthington, Sigourney Weaver, Stephen Lang, Oona Chaplin</span></p>
                        <p id = "duration">Durata: <span>3ore 17min</span></p>
                    </div>
                </div>

                <div class = "time-card">
                    <div id="hours">19:30 - 23:22</div>
                    <div id="hall">Sala 2</div>
                    <div id="tech">Proiezione Laser 4K</div>
                    <div id="price">Da 8,99 €</div>
                </div>


            </main>

        </div>

        <!-- Footer -->
        <footer>
            <p> &copy; 2025 MyCinema</p>            
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