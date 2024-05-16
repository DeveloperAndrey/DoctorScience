<?php session_start();
require 'vendor/autoload.php'; 

include "config.php";

use PhpOffice\PhpSpreadsheet\IOFactory;


$upload_dir = 'uploads/';
// Если файл был успешно загружен
if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {
    $temp_name = $_FILES['uploaded_file']['tmp_name'];
    $original_name = $_FILES['uploaded_file']['name'];

    // Перемещаем файл в папку для загрузок
    if (move_uploaded_file($temp_name, $upload_dir . $original_name)) {
        

// Очистка таблицы specialties
$sql_truncate = "TRUNCATE TABLE specialties";
$conn->query($sql_truncate);

// Чтение данных из файла
$file = $upload_dir . $original_name;
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

// Флаг для отслеживания строки с заголовками
$is_first_row = true;
// Вставка данных из файла Excel в таблицу specialties
foreach ($sheet->getRowIterator() as $row) {
    if ($is_first_row) {
        $is_first_row = false;
        continue; // Пропускаем первую строку с заголовками
    }
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    
    $data = [];
    foreach ($cellIterator as $cell) {
        $data[] = $cell->getValue();
    }
    
    if (!empty($data)) {
        $direction = $conn->real_escape_string($data[0]);
        $old_cipher = $conn->real_escape_string($data[1]);
        $new_cipher = $conn->real_escape_string($data[2]);
        
        $sql_insert = "INSERT INTO specialties (Direction, OldCipher, NewCipher) VALUES ('$direction', '$old_cipher', '$new_cipher')";
        if ($conn->query($sql_insert) === FALSE) {
            die("Ошибка вставки данных: " . $conn->error);
        }
    }
}

echo "Таблица успешно обновлена";
// Закрываем соединение
$conn->close();


    } else {
        echo "Ошибка при перемещении файла.";
    }
} else {
    echo "Ошибка при загрузке файла.";
}



header("Location: setting.php");
exit();
?>