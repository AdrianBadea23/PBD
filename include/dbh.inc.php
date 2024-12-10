<?php
// Database connection setup for Oracle
$dsn = "oci:dbname=//localhost:1521/FREE";
$dbusername = "SYSTEM";
$dbpassword = "root";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
