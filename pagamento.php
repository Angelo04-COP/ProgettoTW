<?php
session_start();
include('db.php'); // Assicurati che $connect sia definita qui

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

// Logica di elaborazione al POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_utente = $_SESSION['id'];
    
    // Inizia una transazione per sicurezza: o si salva tutto o niente
    pg_query($connect, "BEGIN");
    $check_insert = true;

    foreach ($_SESSION['carrello'] as $item) {
        $id_piano = $item['id'];
        
        // Calcolo durata: 30 giorni per i mensili, 365 per il resto
        $durata = (stripos($item['nome'], 'mensile') !== false) ? '30 days' : '365 days';
        
        // Query utilizzando la sintassi PostgreSQL per le date che hai nel DB
        $query = "INSERT INTO abbonamenti (id_utente, id_piano, data_inizio, data_fine, stato) 
                  VALUES ($1, $2, CURRENT_DATE, CURRENT_DATE + INTERVAL '$durata', 'attivo')";
        
        $res = pg_query_params($connect, $query, array($id_utente, $id_piano));
        
        if (!$res) {
            $check_insert = false;
            break; 
        }
    }

    if ($check_insert) {
        pg_query($connect, "COMMIT"); // Conferma i dati nel DB
        unset($_SESSION['carrello']); // Svuota il carrello
        header("Location: profilo.php"); // Vai al profilo 
        exit();
    } else {
        pg_query($connect, "ROLLBACK"); // Cancella inserimenti parziali in caso di errore
        $errore = "Si è verificato un errore tecnico nel salvataggio. Riprova.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Sicuro - MyCinema</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            background-color: #f0f2f5; 
            margin: 0; 
            padding: 40px; 
        }

        .container-carrello { 
            background: white; 
            max-width: 500px; 
            margin: 0 auto; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }

        .form-pagamento { 
            margin-top: 20px; 
            text-align: left; 
        }

        .campo { 
            margin-bottom: 15px; 
            display: flex; 
            flex-direction: column; 
        }

        .campo label { 
            font-size: 14px; 
            color: #555; 
            margin-bottom: 5px; 
            font-weight: 600; 
        }

        .campo input { 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 16px; 
            outline: none; 
            transition: 0.3s; 
        }

        .campo input:focus { 
            border-color: #E50914; 
            box-shadow: 0 0 5px rgba(229, 9, 20, 0.2); 
        }

        .btn-paga { 
            background-color: #28a745; 
            color: white; 
            border: none; 
            padding: 15px; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            width: 100%; 
            transition: 0.3s; 
        }

        .btn-paga:hover { 
            background-color: #218838; 
            transform: translateY(-2px); 
        }

    </style>
</head>
<body>
    <div class="container-carrello">
        <header style="text-align: center; margin-bottom: 30px;">
            <h1>Pagamento 💳</h1>
            <p style="color: #666;">Inserisci i dati della tua carta</p>
            <?php if($errore): ?>
                <p style="color: red; font-weight: bold;"><?php echo $errore; ?></p>
            <?php endif; ?>
        </header>

        <form method="POST" class="form-pagamento">
            <div class="campo">
                <label>Titolare della carta</label>
                <input type="text" name="titolare" placeholder="Mario Rossi" required>
            </div>
            <div class="campo">
                <label>Numero Carta</label>
                <input type="text" name="numero_carta" placeholder="1234 5678 1234 5678" maxlength="16" required>
            </div>
            <div style="display: flex; gap: 20px;">
                <div class="campo" style="flex: 2;">
                    <label>Scadenza</label>
                    <input type="text" name="scadenza" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div class="campo" style="flex: 1;">
                    <label>CVV</label>
                    <input type="text" name="cvv" placeholder="123" maxlength="3" required>
                </div>
            </div>
            <button type="submit" class="btn-paga">Conferma e Paga Ora</button>

            <div style="text-align: center; margin-top: 15px;">
                <a href="carrello.php" style="color: #444; text-decoration: underline; font-size: 14px;">
                    ← Modifica ordine (Torna al carrello)
                </a>
            </div>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #888;">🔒 Pagamento criptato e sicuro</p>
    </div>
</body>
</html>