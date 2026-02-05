<?php
session_start();
include('db.php');

$link_torna_cinema = "";    //link di ritorno alla pagina per selezionare i posti

// Logica per rimuovere un singolo elemento dal carrello
if (isset($_GET['action']) && $_GET['action'] == 'rimuovi' && isset($_GET['id'])) {
    $id_da_rimuovere = (int)$_GET['id'];
    if (isset($_SESSION['carrello'][$id_da_rimuovere])) {
        // Rimuove l'elemento all'indice specificato
        array_splice($_SESSION['carrello'], $id_da_rimuovere, 1);
    }
    header("Location: carrello.php");
    exit();
}

// Logica per svuotare il carrello
if (isset($_GET['action']) && $_GET['action'] == 'svuota') {
    unset($_SESSION['carrello']);
    header("Location: carrello.php");
    exit();
}

if(isset($_SESSION['carrello'])) {
    foreach ($_SESSION['carrello'] as $item) {
        if (isset($item['tipo_item']) && $item['tipo_item'] == 'biglietto') {
            // Costruisco il link di ritorno alla pagina di prenotazione per quella proiezione
            $link_torna_cinema = "prenotazione.php?id=" . $item['id'];
            break;  //appena trovo il primo biglietto esco
        }
    }
}

$totale = 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il tuo Carrello - MyCinema</title>
    <link rel="stylesheet" href="css/style_carrello.css?v=1.4">
</head>
<body>
    <div class="container-carrello">
        <a href="index.php" class="btn-nav">🏠 Home</a>
        <a href="abbonamenti.php" class="btn-nav">← Torna ai piani</a>
        <a href="bar.php" class="btn-nav">← Torna al bar</a>

        <!-- Se esiste un link di ritorno alla pagina cinema, lo mostro -->
        <?php if ($link_torna_cinema!=""): ?>
            <a href="<?php echo $link_torna_cinema; ?>" class="btn-nav">🎟️ Modifica Posti</a>
        <?php endif; ?>

        <h2>Il tuo Carrello</h2>

        <?php if (!isset($_SESSION['carrello']) || count($_SESSION['carrello']) == 0): ?>
            <p>Il carrello è vuoto.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th>Prezzo</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['carrello'] as $index => $item): 
                        $totale += $item['prezzo'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nome']); ?></td>
                        <td><?php echo number_format($item['prezzo'], 2, ',', '.'); ?>€</td>
                        <td style="text-align: center;">
                            <a href="carrello.php?action=rimuovi&id=<?php echo $index; ?>" 
                               class="btn-svuota" 
                               title="Rimuovi prodotto"
                               onclick="return confirm('Vuoi rimuovere questo elemento?')">
                                Rimuovi
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="riepilogo-totale">
                <div class="testo-totale">Totale: <strong><?php echo number_format($totale, 2, ',', '.'); ?>€</strong></div>
                <div class="azioni-carrello">
                    <a href="carrello.php?action=svuota" class="btn-svuota">Svuota Carrello</a>
                    <?php if (isset($_SESSION['id'])): ?>
                        <a href="pagamento.php" class="btn-paga">Procedi al Pagamento</a>
                    <?php else: ?>
                        <p><i>Devi fare il <a href="login.php">login</a> per acquistare.</i></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>