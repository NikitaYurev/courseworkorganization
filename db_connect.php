<?php

$host = 'localhost';
$user = 'root';
$pass = 'Nikitka290620041!';
$dbnm = 'organizationdb';

$conn = new mysqli($host, $user, $pass, $dbnm);

if (!$conn) {
    die('Database connection failed!' . mysqli_connect_error());
}
?>