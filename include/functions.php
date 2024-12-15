<?php
require_once "dbh.inc.php";

// Add a new customer
function addCustomer($numarCard, $nume, $prenume, $dataNasterii) {
    global $pdo;

    $sql = "INSERT INTO customers (NumarCard, Nume, Prenume, DataNasterii) 
            VALUES (:numarCard, :nume, :prenume, TO_DATE(:dataNasterii, 'YYYY-MM-DD'))";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':numarCard' => $numarCard,
        ':nume' => $nume,
        ':prenume' => $prenume,
        ':dataNasterii' => $dataNasterii,
    ]);
    return true;
}

// Add a new product
function addProduct($produs, $garantie, $stoc, $valoareUnitara) {
    global $pdo;

    // Get the next value for the product sequence
    $sql = "SELECT products_seq.NEXTVAL FROM dual";
    $stmt = $pdo->query($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $idProdus = $row['NEXTVAL'];  // Or use the generated value from sequence

    $sql = "INSERT INTO products (IdProdus, Produs, Garantie, Stoc, ValoareUnitara) 
            VALUES (:idProdus, :produs, :garantie, :stoc, :valoareUnitara)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':idProdus' => $idProdus,
        ':produs' => $produs,
        ':garantie' => $garantie,
        ':stoc' => $stoc,
        ':valoareUnitara' => $valoareUnitara,
    ]);
    return true;
}

// Add a new sale
function addSale($numarCard, $idProdus, $cantitate) {
    global $pdo;

    // Start a transaction to ensure atomicity (both actions happen together)
    $pdo->beginTransaction();

    try {
        // Get the next value for the sales sequence
        $sql = "SELECT sales_seq.NEXTVAL FROM dual";
        $stmt = $pdo->query($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $idSales = $row['NEXTVAL'];  // Or use the generated value from sequence

        // Insert sale record
        $sql = "INSERT INTO sales (IdSales, NumarCard, IdProdus, Cantitate, DataVanzarii) 
                VALUES (:idSales, :numarCard, :idProdus, :cantitate, SYSDATE)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idSales' => $idSales,
            ':numarCard' => $numarCard,
            ':idProdus' => $idProdus,
            ':cantitate' => $cantitate,
        ]);

        // Decrement the quantity of the product in the products table
        $sql = "UPDATE products 
                SET stoc = stoc - :cantitate 
                WHERE IdProdus = :idProdus AND stoc >= :cantitate";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cantitate' => $cantitate,
            ':idProdus' => $idProdus,
        ]);

        // If no rows were affected, it means the stock was insufficient
        if ($stmt->rowCount() === 0) {
            // Rollback the transaction and throw an exception if stock is insufficient
            $pdo->rollBack();
            throw new Exception("Insufficient stock for product ID $idProdus.");
        }

        // Commit the transaction
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Retrieve all customers
function getAllCustomers() {
    global $pdo;
    $sql = "SELECT * FROM customers";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve all products
function getAllProducts() {
    global $pdo;
    $sql = "SELECT * FROM products";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve all sales
function getAllSales() {
    global $pdo;
    $sql = "SELECT sales.IdSales, customers.Nume, customers.Prenume, 
                   products.Produs, sales.Cantitate, sales.DataVanzarii 
            FROM sales 
            JOIN customers ON sales.NumarCard = customers.NumarCard 
            JOIN products ON sales.IdProdus = products.IdProdus";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Report for all purchases made by a customer
function getCustomerPurchases($numarCard) {
    global $pdo;
    $sql = "SELECT c.Nume, c.Prenume, p.Produs, s.Cantitate, (s.Cantitate * p.ValoareUnitara) AS ValoareTotala
            FROM sales s
            JOIN customers c ON s.NumarCard = c.NumarCard
            JOIN products p ON s.IdProdus = p.IdProdus
            WHERE c.NumarCard = :numarCard";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':numarCard' => $numarCard]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Report for products sold with warranty not yet expired
function getProductsWithValidWarranty() {
    global $pdo;
    $sql = "SELECT p.Produs, ADD_MONTHS(s.DataVanzarii, p.Garantie * 12) AS DataExpirarii
            FROM sales s
            JOIN products p ON s.IdProdus = p.IdProdus
            WHERE ADD_MONTHS(s.DataVanzarii, p.Garantie) > SYSDATE";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Best selling product
function getBestSellingProduct() {
    global $pdo;
    $sql = "SELECT p.Produs, SUM(s.Cantitate) AS TotalVandut
            FROM sales s
            JOIN products p ON s.IdProdus = p.IdProdus
            GROUP BY p.Produs
            ORDER BY TotalVandut DESC
            FETCH FIRST 1 ROWS ONLY";
    return $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
}

// Date with the highest number of sales
function getDateWithMostSales() {
    global $pdo;
    $sql = "SELECT TRUNC(s.DataVanzarii) as DataVanzarii, SUM(s.Cantitate) AS NumarVanzari
            FROM sales s
            GROUP BY TRUNC(s.DataVanzarii)
            ORDER BY NumarVanzari DESC
            FETCH FIRST 1 ROWS ONLY";
    return $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
}

// Customer who bought the most products and total value of purchases
function getTopCustomer() {
    global $pdo;
    $sql = "SELECT c.Nume, c.Prenume, SUM(s.Cantitate * p.ValoareUnitara) AS ValoareTotala
            FROM sales s
            JOIN customers c ON s.NumarCard = c.NumarCard
            JOIN products p ON s.IdProdus = p.IdProdus
            GROUP BY c.Nume, c.Prenume
            ORDER BY ValoareTotala DESC
            FETCH FIRST 1 ROWS ONLY";
    return $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
}
?>
