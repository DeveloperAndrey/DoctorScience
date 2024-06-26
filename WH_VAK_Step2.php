<?php
// Выполнить Python скрипт и получить вывод
// $output будет содержать как стандартный вывод, так и сообщения об ошибках
exec("python3 VAK_Step2.py 2>&1", $output, $returnCode);

// Если $returnCode равен 0, это означает успешное выполнение скрипта
if ($returnCode === 0) {
    echo "Скрипт успешно выполнен:<br>";
    // Вывести стандартный вывод скрипта
    foreach ($output as $line) {
        echo $line . "<br>";
    }
} else {
    // Если $returnCode не равен 0, это означает, что произошла ошибка при выполнении скрипта
    echo "Произошла ошибка при выполнении скрипта:<br>";
    // Вывести сообщения об ошибках
    foreach ($output as $error) {
        echo $error . "<br>";
    }
}
?>