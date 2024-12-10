<?php
// sales.php
require_once "../include/functions.php";

// Fetch sales
$sales = getAllSales();
$salesWithWarranty = getProductsWithValidWarranty();
$topCustomer = getTopCustomer();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === "addSale") {
    $numarCard = $_POST['numarCard'];
    $idProdus = $_POST['idProdus'];
    $cantitate = $_POST['cantitate'];

    try {
        addSale($numarCard, $idProdus, $cantitate);
        $sales = getAllSales(); // Refresh the sales list after adding
        $salesWithWarranty = getProductsWithValidWarranty(); // Refresh the sales list after adding
        $topCustomer = getTopCustomer(); // Refresh top customer after adding
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Management vanzari</title>
    <style>
    </style>
</head>
<body>

<h1>Toate Vanzarile</h1>

<form method="POST">
    <input type="hidden" name="action" value="addSale">
    Numar Card: <input type="number" name="numarCard" min="10000000" max="99999999" required><br>
    ID Produs: <input type="number" name="idProdus" required><br>
    Cantitate: <input type="number" name="cantitate" min="1" required><br>
    <button type="submit">Adauga Vanzare</button>
</form>

<h2>Toate Vanzarile</h2>
<table>
    <thead>
        <tr>
            <th>Nume</th>
            <th>Prenume</th>
            <th>Produs</th>
            <th>Cantitate</th>
            <th>Data Vanzarii</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?= htmlspecialchars($sale['NUME']) ?></td>
                <td><?= htmlspecialchars($sale['PRENUME']) ?></td>
                <td><?= htmlspecialchars($sale['PRODUS']) ?></td>
                <td><?= htmlspecialchars($sale['CANTITATE']) ?></td>
                <td><?= htmlspecialchars($sale['DATAVANZARII']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Produse vandute pentru care nu a expirat inca garantia</h2>
<table>
    <thead>
        <tr>
            <th>Produs</th>
            <th>Data Expirarii</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($salesWithWarranty as $sale): ?>
            <tr>
                <td><?= htmlspecialchars($sale['PRODUS']) ?></td>
                <td><?= htmlspecialchars($sale['DATAEXPIRARII']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Clientul care a cumparat cele mai multe produse</h2>
<table>
    <thead>
        <tr>
            <th>Nume</th>
            <th>Prenume</th>
            <th>Valoare Totala</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($topCustomer): ?>
            <tr>
                <td><?= htmlspecialchars($topCustomer['NUME']) ?></td>
                <td><?= htmlspecialchars($topCustomer['PRENUME']) ?></td>
                <td><?= htmlspecialchars($topCustomer['VALOARETOTALA']) ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="3">Nu sunt clienti inregistrati.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
