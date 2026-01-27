<?php
session_start();
include('db.php'); // connessione al database gruppo15

// Gestione dell'aggiunta al carrello
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_prod'])) {
    $id_scelto = (int)$_POST['id_prod'];
    
    // Recupero info dal DB
    $res = pg_query_params($connect, "SELECT * FROM prodotti_bar WHERE id = $1", array($id_scelto));
    $prodotto = pg_fetch_assoc($res);

    if ($prodotto) {
        // Aggiungiamo il tipo per coerenza nel carrello globale
        $prodotto['tipo_item'] = 'bar';
        $_SESSION['carrello'][] = $prodotto;
        header("Location: bar.php?success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>MyCinema - Bar & Snack</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
        
        body { 
            background-color: black; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
        }

        header { background-color: black; padding: 15px; width: 100%; }

        .horizontal-nav {
            list-style-type: none;
            background: linear-gradient(to bottom, #2f3a52, #262f43); /* Gradiente nav */
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .horizontal-nav li a {
            display: block;
            color: white;
            padding: 20px;
            text-decoration: none;
        }

        .horizontal-nav li:hover { background-color: #333; }

        /* Contenitore principale con gradiente radiale */
        .main-bar {
            flex: 1;
            padding: 40px;
            background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);
            color: white;
            text-align: center;
        }

        .bar-header { margin-bottom: 40px; }
        .bar-header h1 { color: #ff9d00; font-size: 32px; text-transform: uppercase; }

        /* Visualizzazione prodotti "in cascata" (Grid) */
        .prodotti-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-prodotto {
            background-color: #111;
            border: 1px solid #222;
            padding: 20px;
            border-radius: 10px;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card-prodotto:hover { border-color: #ff9d00; transform: scale(1.02); }

        .card-prodotto img {
            width: 100%;
            height: 180px;
            object-fit: cover; /* Mantiene le proporzioni delle immagini */
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #333;
        }

        .card-prodotto h3 { color: #ff9d00; margin-bottom: 10px; }
        
        .prezzo-tag { font-size: 20px; font-weight: bold; margin-bottom: 15px; }

        .btn-buy {
            background-color: #ff9d00;
            color: black;
            border: none;
            padding: 12px 25px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            width: 100%;
            border-radius: 5px;
        }

        .btn-buy:hover { background-color: #e68a00; }

        /* Bottone Carrello */
        .btn-carrello-float {
            display: inline-block;
            margin-top: 20px;
            background: #333;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid #ff9d00;
            font-weight: bold;
        }

        .btn-carrello-float:hover { background: #ff9d00; color: black; }

        footer {
            color: white;
            background-color: #000;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #333;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <ul class="horizontal-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="abbonamenti.php">Abbonamenti</a></li>
            <li><a href="bar.php" style="border-bottom: 2px solid #ff9d00;">Bar</a></li>
            <?php if(isset($_SESSION['username'])): ?>
                <li style="color:white; padding:20px;">Ciao, <strong><?php echo $_SESSION['username']; ?></strong></li>
                <li><a href="logout.php" style="color: #ff4444;">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Accedi</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="main-bar">
    <div class="bar-header">
        <h1>🍿 Bar del Cinema</h1>
        <p>Aggiungi snack e bibite al tuo ordine!</p>
        <a href="carrello.php" class="btn-carrello-float">
            🛒 VAI AL CARRELLO (<?php echo isset($_SESSION['carrello']) ? count($_SESSION['carrello']) : 0; ?>)
        </a>
    </div>

    <div class="prodotti-container">
        <?php
        $query = "SELECT * FROM prodotti_bar ORDER BY categoria, id";
        $result = pg_query($connect, $query);
        
        while($row = pg_fetch_assoc($result)){
            ?>
            <div class="card-prodotto">
                <img src="img/<?php echo $row['immagine']; ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>">
                
                <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
                <p class="prezzo-tag"><?php echo number_format($row['prezzo'], 2, ',', '.'); ?>€</p>
                
                <?php if(isset($_SESSION['username'])): ?>
                    <form method="POST">
                        <input type="hidden" name="id_prod" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn-buy">Aggiungi al Carrello</button>
                    </form>
                <?php else: ?>
                    <p style="color: #666; font-size: 14px;">Effettua il login per acquistare</p>
                <?php endif; ?>
            </div>
            <?php
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2026 MyCinema - Tutti i diritti riservati</p>
</footer>

</body>
</html>