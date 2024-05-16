<?php session_start(); 
require 'vendor/autoload.php';
include "config.php";

$sql = "SELECT `Direction` AS 'Направление подготовки', `OldCipher` AS 'Научные специальности (старый шифр)', `NewCipher` AS 'Научные специальности (новый шифр)' FROM `specialties` ORDER BY `Direction` ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Получаем все данные из результата запроса в виде массива
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Создание нового объекта PHPExcel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Добавление заголовков
    $column = 'A';
    foreach ($result->fetch_fields() as $field) {
        $sheet->setCellValue($column . '1', $field->name);
        $column++;
    }

    // Добавление данных из массива
    $row = 2;
    foreach ($data as $row_data) {
        $column = 'A';
        foreach ($row_data as $cell_data) {
            $sheet->setCellValue($column . $row, $cell_data);
            $column++;
        }
        $row++;
    }

    // Сохранение файла Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = 'Направления.xlsx';
    $writer->save($filename);

    // Отправка заголовков для загрузки файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);

}

// Закрываем соединение
$conn->close();
header("Location: setting.php");
exit();
?>