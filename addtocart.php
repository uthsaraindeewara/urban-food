<?php
session_start();

include "connection.php";

// Get data from POST request
$productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
// $cusId = $_SESSION['user']['cusID'];
$cusId = 1;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

$sql = "BEGIN SYSTEM.add_product_to_cart(:productId, :cusId, :quantity); END;";

$stid = oci_parse($conn, $sql);

oci_bind_by_name($stid, ":productId", $productId);
oci_bind_by_name($stid, ":cusId", $cusId);
oci_bind_by_name($stid, ":quantity", $quantity);

// Execute the procedure
if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Error executing procedure: " . $e['message']);
}

oci_free_statement($stid);
oci_close($conn);
?>