<?php
session_start();
include "config.php";
if(isset($_SESSION['admin']) && $_SESSION['admin']) {
    if (isset($_GET['pub_base']) && isset($_GET['pub_id'])) {
        $pub_base = $_GET['pub_base'];
        $pub_id = $_GET['pub_id'];
        $sql = "DELETE FROM $pub_base WHERE id = $pub_id";
        $result = $conn->query($sql);
    }
}

// Перенаправление пользователя
header("location: http://localhost/doctor/docktor.php?id={$_GET['id']}");
?>