<?php
// config.php

$host = "localhost:3306";
$username = "admin_doctors";
$password = "Lg8y{:P#(]RPieFM";
$database = "admin_doctors";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка соединения с базой данных: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
