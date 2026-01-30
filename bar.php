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
    <link rel="stylesheet" type="text/css" href="style_bar.css?ver=1.2" />
</head>
<body>

<header>
    <nav>
        <ul class="horizontal-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="abbonamenti.php">Abbonamenti</a></li>
            <li><a href="bar.php" style="border-bottom: 2px solid #ff9d00;">Bar</a></li>
            <?php if(isset($_SESSION['username'])): ?>
                <li style="color:black; padding:20px;">Benvenuto, <strong><?php echo $_SESSION['username']; ?></strong></li>
                <li><a href="logout.php" style="color: #991f1fff;">Logout</a></li>
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
        $query = "SELECT * FROM prodotti_bar ORDER BY categoria, nome";
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