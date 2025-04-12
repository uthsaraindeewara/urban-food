<?php

$conn = oci_connect('SYSTEM', 'ui123', 'DESKTOP-5EIEBIJ/XE');

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}
?>