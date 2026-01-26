<?php 
    session_start();
?>

<html lang = "it">
    <head>
        <title>MyCinema : IL MIO PROFILO</title>
       <meta charset = "utf-8" />
        <style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;

            }
            body{
                display: flex;              /*Visualizza l'elemento come un contenitore "flessibile" */
                background-color: #111;     /*colore di sfondo del body*/
                justify-content: center;
                align-items: center;
                min-height: 100px;
                padding: 90px;
                color: white;


            }

            .container{
                display: flex;             /*Visualizza l'elemento come un contenitore "flessibile" */
                justify-content: center;    /*Centra il div*/
                align-items: center;        /*alline gli elementi del div al centro*/
                padding: 20px;
                width: 750px;
               

            }

            .profile-sec{
                background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);  /*colore di sfondo della sezione per il profilo*/
                border: 1px solid #333;
                border-top: 5px solid #ff9d00;
                padding: 80px;
                width: 100%;
                text-align: center;
                border-radius: 15px;    /*proprietà che definisce il raggio degli angoli dell'elemento, permette di aggiungere angoli arrotondati ai bordi*/ 
                margin: 50px auto;  /*top e bottom margin di 50 px ; left e right margin auto per centrare orizzontalmente l'elemento nel suo contenitore*/

            }

            h1{
                color: #ff9d00;
                font-size: 25px;
                margin-bottom: 30px;
                letter-spacing: 1px;      /*aumento lo spazio tra le lettere*/
                text-transform: uppercase;

            }

            .profile-info{
                text-align: left;
                padding: 15px;
                border-radius: 10px;
                margin-bottom: 30px;
            }

            p{
                background-color: #222;
                color: white;
                margin: 15px 0;
                border-bottom: 1px solid #222;
                padding: 20px;
                display: flex;
                justify-content: space-between  /*allinea etichetta a sinistra e valore a destra*/ 

            }

            .label{
                color: #ff9d00;
                font-weight: bold;
                width: 100px;
                font-size: 15px;
                text-transform: uppercase;

            }

            a{
                color: #ff9d00;
                margin-top: 25px;
                font-weight: bold;
                text-decoration: none;
                font-size: 15px;
            }
            
           

        </style>
    </head>

    <body>
        <div class="container">
            <div class="profile-sec">
                <h1>Dati del profilo</h1>
                <div class = "profile-info">
                    <p><span class = "label">Username: </span> <?php echo $_SESSION['username']; ?></p>
                    <p><span class = "label">Nome: </span> <?php echo $_SESSION['nome']; ?></p>
                    <p><span class = "label">Cognome: </span><?php echo $_SESSION['cognome']; ?></p>
                    <p><span class = "label">Email: </span> <?php echo $_SESSION['email']; ?></p> 
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