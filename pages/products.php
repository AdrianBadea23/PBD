<?php
// products.php
require_once "../include/functions.php";

// Fetch products
$products = getAllProducts();

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

</body>
</html>
