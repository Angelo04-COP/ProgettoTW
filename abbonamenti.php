<?php
session_start();
include('db.php'); // connessione al database gruppo15 

// Recupero dei piani dal database 
$query = "SELECT id, nome, prezzo, descrizione AS desc FROM piani ORDER BY id ASC";
$result = pg_query($connect, $query);
$piani = pg_fetch_all($result);

// Se il database è vuoto, inizializzio un array vuoto 
if (!$piani) { $piani = []; }

// Controllo piani già attivi nel DB ---
$id_utente = $_SESSION['id'] ?? null;
$piani_gia_attivi = [];

if ($id_utente) {
    // Cerchiamo gli ID dei piani che l'utente ha già acquistato e sono ancora attivi
    $query_check = "SELECT id_piano FROM abbonamenti 
                    WHERE id_utente = $1 
                    AND stato = 'attivo' 
                    AND data_fine >= CURRENT_DATE";
    $res_check = pg_query_params($connect, $query_check, array($id_utente));
    if ($res_check) {
        $piani_gia_attivi = pg_fetch_all_columns($res_check) ?: [];
    }
}


$errore = "";

// Gestione dell'aggiunta al carrello
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_piano'])) {
    $id_scelto = (int)$_POST['id_piano'];
    $gia_presente_in_sessione = false;
    
    // Controllo se è già nel carrello (sessione)
    if (isset($_SESSION['carrello'])) {
        foreach ($_SESSION['carrello'] as $item) {
            if ($item['id'] == $id_scelto) { 
                $gia_presente_in_sessione = true; 
                break; 
            }
        }
    }
    
    // Controllo finale: non deve essere né nel carrello né già attivo nel DB
    if (!$gia_presente_in_sessione && !in_array($id_scelto, $piani_gia_attivi)) {
        foreach($piani as $p) {
            if($p['id'] == $id_scelto) {
                $p['tipo_item'] = 'abbonamento';
                $_SESSION['carrello'][] = $p;
                header("Location: abbonamenti.php?aggiunto=1");
                exit();
            }
        }
    } elseif (in_array($id_scelto, $piani_gia_attivi)) {
        $errore = "Hai già questo abbonamento attivo!";
    } else {
        $errore = "Questo abbonamento è già nel tuo carrello!";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Abbonamenti MyCinema</title>
    <link rel="stylesheet" href="abbonamenti.css">
</head>
<body>
    <div class="header-nav">
        <a href="index.php" class="btn-nav">🏠 Home</a>
        <h1 id="main-title">ABBONAMENTI MyCINEMA</h1>
        <a href="carrello.php" class="btn-nav">🛒 Carrello (<?php echo isset($_SESSION['carrello']) ? count($_SESSION['carrello']) : 0; ?>)</a>
    </div>

    <?php if($errore): ?>
        <div style="color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: center; max-width: 1200px; margin: 0 auto 20px;">
            <?php echo $errore; ?>
        </div>
    <?php endif; ?>

    <div class="container">
    <?php foreach ($piani as $p): ?>
        <div class="card <?php echo (strpos($p['nome'], 'Premium') !== false) ? 'premium' : ''; ?>">
            
            <?php if(strpos($p['nome'], 'Premium') !== false): ?>
                <div class="badge-premium">PREMIUM</div>
            <?php endif; ?>

            <div class="info">
                <h3><?php echo htmlspecialchars($p['nome']); ?></h3>
                <p class="price"><?php echo number_format($p['prezzo'], 2, ',', '.'); ?>€</p>
                <p class="desc"><?php echo htmlspecialchars($p['desc']); ?></p>
            </div>

            <form method="POST">
                <input type="hidden" name="id_piano" value="<?php echo $p['id']; ?>">
                
                <?php if (in_array($p['id'], $piani_gia_attivi)): ?>
                    <button type="button" class="btn" disabled>Attivo</button>
                <?php else: ?>
                    <button type="submit" class="btn">Scegli</button>
                <?php endif; ?>
            </form>
       </div>
    <?php endforeach; ?>
    </div>
</body>
</html>
