<?php

$conn = oci_connect('test_user','1234','//localhost:1521/xepdb1');

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}
?>