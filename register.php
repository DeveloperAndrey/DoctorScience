<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "config.php";

// Обработка формы регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $fio = $_POST["fio"];
    $mail = $_POST["mail"];

    $sql = "INSERT INTO `users`(`fio`, `login`, `password`, `mail`) VALUES ('$fio','$login', '$password','$mail')";

    if ($conn->query($sql) === TRUE) {
        // echo "Регистрация успешна!";
        // echo "<br><a href='authorization.html'>Пройти авторизацию</a>";
        header("Location: index.php");
    } else {
        // echo "Ошибка: " . $sql . "<br>" . $conn->error;
        header("Location: index.php");
    }
}

$conn->close();
?>
