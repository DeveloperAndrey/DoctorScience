<?php session_start(); 
require 'vendor/autoload.php';
include "config.php";

$yearStart = $conn->query("SELECT `start_year` FROM `setting`")->fetch_assoc()['start_year'];
$year = $conn->query("SELECT `end_year` FROM `setting`")->fetch_assoc()['end_year'];

$sql = "SELECT 
    doctors.`Претенденты ФИО`,
    doctors.`Шифр научной специальности, по которой защищена диссертация`,
    doctors.`Ссылка на ВАК`,
    doctors.`Ссылка на Elibrary`,
    doctors.`Ссылка на Dissernet`,
    doctors.`Авторефират`,
    doctors.`Год защиты`,
    doctors.`Место работы`,
    doctors.`Тема диссертации`,
    doctors.`Место защиты`,
    doctors.`Отрасль науки`,
    doctors.`Приказ`,
    doctors.`Решение`,
    doctors.`Колличество публикаций на Elibrary`,
    doctors.`Author ID`,
    doctors.`SPIN-код`,
    COALESCE(vak_counts.VAK, 0) AS vak,
    COALESCE(rsci_counts.rsci, 0) AS rsci,
    COALESCE(wos_counts.wos, 0) AS wos,
    COALESCE(vak_counts.K1, 0) AS K1,
    COALESCE(vak_counts.K2, 0) AS K2,
    COALESCE(vak_counts.K3, 0) AS K3,
    doctors.`Свои диссертации (Dissernet)`,
    doctors.`Чужие диссертации (Dissernet)`,
    doctors.`Публикации (Dissernet)`
    
FROM 
    doctors 
LEFT JOIN 
    (
        SELECT 
            link_autor,
            COUNT(year) AS VAK,
            SUM(CASE WHEN vakcategory2023.Category = 'К1' THEN 1 ELSE 0 END) AS K1,
            SUM(CASE WHEN vakcategory2023.Category = 'К2' THEN 1 ELSE 0 END) AS K2,
            SUM(CASE WHEN vakcategory2023.Category = 'К3' THEN 1 ELSE 0 END) AS K3
        FROM 
            vak 
        LEFT JOIN 
            vakcategory2023 ON vak.journal = vakcategory2023.Name
        WHERE 
            vak.year > $yearStart
        GROUP BY 
            link_autor
    ) AS vak_counts ON doctors.`Ссылка на Elibrary` = vak_counts.link_autor
LEFT JOIN 
    (
        SELECT 
            link_autor,
            COUNT(year) AS rsci
        FROM 
            rsci
        WHERE 
            year > $yearStart
        GROUP BY 
            link_autor
    ) AS rsci_counts ON doctors.`Ссылка на Elibrary` = rsci_counts.link_autor
LEFT JOIN 
    (
        SELECT 
            link_autor,
            COUNT(year) AS wos
        FROM 
            wos
        WHERE 
            year > $yearStart
        GROUP BY 
            link_autor
    ) AS wos_counts ON doctors.`Ссылка на Elibrary` = wos_counts.link_autor
             GROUP BY `Претенденты ФИО`, `Тема диссертации`, `Год защиты`
                ORDER BY `Претенденты ФИО`";
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
    $filename = 'Доктора.xlsx';
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
header("Location: docktors.php");
exit();
?>