<?php
// products.php
require_once "../include/functions.php";

// Fetch products
$products = getAllProducts();
$customerPurchases = [];
$bestProduct = getBestSellingProduct();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === "addProduct") {
    $produs = $_POST['produs'];
    $garantie = $_POST['garantie'];
    $stoc = $_POST['stoc'];
    $valoareUnitara = $_POST['valoareUnitara'];

    try {
        addProduct($produs, $garantie, $stoc, $valoareUnitara);
        $products = getAllProducts(); // Refresh the product list after adding
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['numarCard'])) {
    $numarCard = htmlspecialchars($_POST['numarCard']);
    $customerPurchases = getCustomerPurchases($numarCard);
} else {
    $customerPurchases = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Management Produse</title>
    <style>
    </style>
</head>
<body>

<h1>Toate produsele</h1>

<form method="POST">
    <input type="hidden" name="action" value="addProduct">
    Produs: <input type="text" name="produs" required><br>
    Garantie: <input type="number" name="garantie" min="1" max="5" required><br>
    Stoc: <input type="number" name="stoc" min="0" max="200" required><br>
    Valoare Unitara: <input type="number" name="valoareUnitara" step="0.01" required><br>
    <button type="submit">Adauga Produs</button>
</form>

<h2>Toate produsele</h2>
<table>
    <thead>
        <tr>
            <th>ID Produs</th>
            <th>Produs</th>
            <th>Garantie</th>
            <th>Stoc</th>
            <th>Valoare Unitara</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['IDPRODUS']) ?></td>
                <td><?= htmlspecialchars($product['PRODUS']) ?></td>
                <td><?= htmlspecialchars($product['GARANTIE']) ?></td>
                <td><?= htmlspecialchars($product['STOC']) ?></td>
                <td><?= htmlspecialchars($product['VALOAREUNITARA']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<h2>Produsele cumparate de un client</h2>
    <form method="POST" action="">
        <label for="numarCard">Numar Card:</label>
        <input type="text" id="numarCard" name="numarCard" required>
        <button type="submit">Afiseaza Produse</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Produs</th>
                <th>Cantitate</th>
                <th>Valoare Totala</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($customerPurchases)): ?>
                <?php foreach ($customerPurchases as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['NUME']) ?></td>
                        <td><?= htmlspecialchars($product['PRENUME']) ?></td>
                        <td><?= htmlspecialchars($product['PRODUS']) ?></td>
                        <td><?= htmlspecialchars($product['CANTITATE']) ?></td>
                        <td><?= htmlspecialchars(number_format($product['VALOARETOTALA'], 2)) ?> RON</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nu s-au găsit produse pentru numărul de card specificat.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>


    <h2>Cel mai bine vandut produs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Produs</th>
                <th>Total Vandut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bestProduct)): ?>
                    <tr>
                        <td><?= htmlspecialchars($bestProduct['PRODUS']) ?></td>
                        <td><?= htmlspecialchars($bestProduct['TOTALVANDUT']) ?></td>
                    </tr>
            <?php endif; ?>
        </tbody>
    </table>


</body>
</html>
