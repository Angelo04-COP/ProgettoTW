<?php
session_start();
include('db.php');

if (isset($_GET['action']) && $_GET['action'] == 'svuota') {
    unset($_SESSION['carrello']);
    header("Location: carrello.php");
    exit();
}
$totale = 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il tuo Carrello - MyCinema</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            background-color: #1d1c1cff; 
            margin: 0; 
            padding: 40px; 
        }

        .container-carrello { 
            background: #c0bdbdff; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }

        h2 { 
            color: #0b0b0bff; 
            margin-bottom: 20px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }

        th { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 2px solid #aeacacff; 
            color: #0d0d0dff; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 14px; 
        }

        td { 
            padding: 20px 15px; 
            text-align: left; 
            border-bottom: 1px solid #c7c5c5ff; 
            font-weight: normal; 
            color: #313030ff; 
        }

        .riepilogo-totale { 
            margin-top: 25px; 
            text-align: right; 
            padding-top: 15px; 
        }

        .testo-totale { 
            font-size: 24px; 
            font-weight: 300; 
            color: #050505ff; 
            margin-bottom: 15px; 
        }

        .testo-totale strong { 
            font-weight: 600; 
            color: #000000ff; 
        }

        .azioni-carrello { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 20px; 
        }

        .btn-paga { 
            background-color: #c1782fff; 
            color: white; 
            border: none; 
            padding: 10px 25px; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            text-decoration: none; 
            transition: 0.3s ease; 
        }

        .btn-paga:hover { 
            background-color: #c1782fff; 
            transform: translateY(-2px); 
            box-shadow: 0 4px 10px rgba(40,167,69,0.2); 
        }

        .btn-svuota { 
            color: #723411ff; 
            text-decoration: none; 
            font-size: 14px; 
        }

        .btn-svuota:hover { 
            text-decoration: underline; 
        }

        .btn-nav { 
            text-decoration: none; 
            background: #646363ff; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 8px; 
            display: inline-block; 
            margin-bottom: 20px; 
        }

    </style>
</head>
<body>
    <div class="container-carrello">
        <a href="abbonamenti.php" class="btn-nav">← Torna ai piani</a>
        <a href="bar.php" class="btn-nav">← Torna al bar</a>
        <h2>Il tuo Carrello</h2>

        <?php if (!isset($_SESSION['carrello']) || count($_SESSION['carrello']) == 0): ?>
            <p>Il carrello è vuoto.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Abbonamento</th>
                        <th>Prezzo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['carrello'] as $item): 
                        $totale += $item['prezzo'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nome']); ?></td>
                        <td><?php echo number_format($item['prezzo'], 2, ',', '.'); ?>€</td>
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