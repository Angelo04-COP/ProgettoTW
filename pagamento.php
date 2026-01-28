<?php
session_start();
include('db.php'); 

// Controllo sicurezza: Utente loggato e Carrello non vuoto
if (!isset($_SESSION['id'])) {
    header("Location: login.php?msg=necessario_login");
    exit();
}

if (empty($_SESSION['carrello'])) {
    header("Location: abbonamenti.php");
    exit();
}

$errore = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_utente = $_SESSION['id'];
    
    pg_query($connect, "BEGIN");
    $check_insert = true;

    foreach ($_SESSION['carrello'] as $item) {
        if (isset($item['tipo_item']) && $item['tipo_item'] == 'bar') {
            // Inserimento per i prodotti del BAR
            $query = "INSERT INTO acquisti_bar (utente_id, prodotto_id, prezzo_pagato) VALUES ($1, $2, $3)";
            $res = pg_query_params($connect, $query, array($id_utente, $item['id'], $item['prezzo']));
        }

        // Inserimento biglietti del cinema
        elseif (isset($item['tipo_item']) && $item['tipo_item'] == 'biglietto') {
            //salvataggio in tabella prenotazioni DB
            //NOTA: $item['id'] in questo caso si riferisce all'id della prenotazione biglietto
            $query = "INSERT INTO prenotazioni (proiezione_id, utente_id, fila, numero) VALUES ($1, $2, $3, $4)"; 
            $res = pg_query_params($connect, $query, array($item['id'], $id_utente, $item['fila'], $item['numero']));
        }
        
        else {
            // Inserimento per gli ABBONAMENTI (Logica originale)
            // Calcolo durata: 30 giorni per i mensili, 365 per il resto
            $durata = (stripos($item['nome'], 'mensile') !== false) ? '30 days' : '365 days';
            $query = "INSERT INTO abbonamenti (id_utente, id_piano, data_inizio, data_fine, stato) 
                      VALUES ($1, $2, CURRENT_DATE, CURRENT_DATE + INTERVAL '$durata', 'attivo')";
            $res = pg_query_params($connect, $query, array($id_utente, $item['id']));
        }
        
        if (!$res) {
            $check_insert = false;
            break; 
        }
    }

    if ($check_insert) {
        pg_query($connect, "COMMIT");
        unset($_SESSION['carrello']);
        header("Location: profilo.php"); 
        exit();
    } else {
        pg_query($connect, "ROLLBACK");
        $errore = "Si è verificato un errore tecnico nel salvataggio. Riprova.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Sicuro - MyCinema</title>
    <link rel="stylesheet" href="style_pagamento.css?v=1.1">

</head>
<body>
    <div class="container-carrello">
        <header style="text-align: center; margin-bottom: 30px;">
            <h1>Pagamento 💳</h1>
            <p style="color: #a8a8a8ff;">Inserisci i dati della tua carta</p>
            <?php if($errore): ?>
                <p style="color: red; font-weight: bold;"><?php echo $errore; ?></p>
            <?php endif; ?>
        </header>

        <form method="POST" name="formPagamento" class="form-pagamento" onsubmit="return validaPagamento(this);">
            <div class="campo">
                <label>Titolare della carta</label>
                <input type="text" name="titolare" placeholder="Mario Rossi">
            </div>
            
            <div class="campo">
                <label>Numero Carta</label>
                <input type="text" name="numero_carta" placeholder="1234567812345678" maxlength="16">
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="campo" style="flex: 2;">
                    <label>Scadenza</label>
                    <input type="text" name="scadenza" placeholder="MM/AA" maxlength="5">
                </div>
                <div class="campo" style="flex: 1;">
                    <label>CVV</label>
                    <input type="text" name="cvv" placeholder="123" maxlength="3">
                </div>
            </div>

            <button type="submit" class="btn-paga">Conferma e Paga Ora</button>

            <div style="text-align: center; margin-top: 15px;">
                <a href="carrello.php" style="color: #d9d8d8ff; text-decoration: underline; font-size: 14px;">
                    ← Modifica ordine (Torna al carrello)
                </a>
            </div>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #a7a7a7ff;">🔒 Pagamento criptato e sicuro</p>
    </div>
    <script src="validazione_pagamento.js"></script>
</body>
</html>