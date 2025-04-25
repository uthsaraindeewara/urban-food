<?php

$conn = oci_connect('SYSTEM', 'ui123', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}
?>