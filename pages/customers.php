<?php
// customers.php
require_once "../include/functions.php";

// Fetch customers
$customers = getAllCustomers();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === "addCustomer") {
    $numarCard = $_POST['numarCard'];
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $dataNasterii = $_POST['dataNasterii'];

    try {
        addCustomer($numarCard, $nume, $prenume, $dataNasterii);
        $customers = getAllCustomers(); // Refresh the customer list after adding
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
    <title>Management cumparatori</title>
    <style>
    </style>
</head>
<body>

<h1>Toti Clientii</h1>

<!-- Form to Add Customer -->
<form method="POST">
    <input type="hidden" name="action" value="addCustomer">
    Numar Card: <input type="number" name="numarCard" min="10000000" max="99999999" required><br>
    Nume: <input type="text" name="nume" required><br>
    Prenume: <input type="text" name="prenume" required><br>
    Data Nasterii (YYYY-MM-DD): <input type="date" name="dataNasterii" required><br>
    <button type="submit">Adauga Client</button>
</form>

<h2>Toti Clientii</h2>
<table>
    <thead>
        <tr>
            <th>Numar Card</th>
            <th>Nume</th>
            <th>Prenume</th>
            <th>Data Nasterii</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= htmlspecialchars($customer['NUMARCARD']) ?></td>
                <td><?= htmlspecialchars($customer['NUME']) ?></td>
                <td><?= htmlspecialchars($customer['PRENUME']) ?></td>
                <td><?= htmlspecialchars($customer['DATANASTERII']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
