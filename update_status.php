<?php
session_start();

include "connection.php";

if (isset($_POST['updateStatus'])) {
    $orderItemID = $_POST['updateStatus'];

   
    $stmt = oci_parse($conn, "BEGIN UpdateOrderItemStatus(:orderItemID, 'SHIPPED'); END;");
    oci_bind_by_name($stmt, ":orderItemID", $orderItemID);
    oci_execute($stmt);
     
    oci_commit($conn);

    header("Location: seller_orders.php");
    exit;
}
?>