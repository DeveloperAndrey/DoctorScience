<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE login='$login'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            
            $_SESSION["login"] = $login;
            $_SESSION["fio"] = $row["fio"];
            $_SESSION["admin"] = $row["admin"];
            header("Location: index.php");
            // Редирект на защищенную страницу или выполнение других действий
        } else {
            // echo "Неверный пароль";
            // echo "<br><a href='authorization.html'>Вернуться</a>";
            header("Location: index.php");
        }
    } else {
        // echo "Пользователь не найден";
        // echo "<br><a href='authorization.html'>Вернуться</a>";
        header("Location: index.php");
    }

}

$conn->close();
?>
