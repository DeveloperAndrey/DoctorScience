<?php 
session_start(); 
include "config.php";
$OFFSET = 0;
if(isset($_GET['action'])){
    switch ($_GET['action']) {
        case 'search':
        	$OFFSET += isset($_GET['offset']) ? 10 : 0;
            $sql = "SELECT `id`,`Ссылка на Elibrary`,`Претенденты ФИО`,`Колличество публикаций на Elibrary` FROM `doctors` WHERE `Ссылка на Elibrary` = '' LIMIT 10 OFFSET $OFFSET";
            $result = $conn->query($sql);
            break;
        case 'collect':
        	$OFFSET += isset($_GET['offset']) ? 10 : 0;
            $sql = "
                SELECT 
                    `id`,
                    `Претенденты ФИО`,
                    `Ссылка на Elibrary`,
                    `Колличество публикаций на Elibrary`,
                    `Место работы`,
                    `Author ID`,
                    `SPIN-код`,
                    COALESCE(vak_counts.VAK, 0) AS vak,
                    COALESCE(rsci_counts.rsci, 0) AS rsci,
                    COALESCE(wos_counts.wos, 0) AS wos
                FROM `doctors` 
                LEFT JOIN 
                    (
                        SELECT 
                        link_autor,
                        COUNT(link_autor) AS VAK
                        FROM 
                            vak
                        GROUP BY 
                            link_autor
                    ) AS vak_counts ON doctors.`Ссылка на Elibrary` = vak_counts.link_autor 
                LEFT JOIN 
                    (
                        SELECT 
                        link_autor,
                        COUNT(link_autor) AS rsci
                        FROM 
                            rsci
                        GROUP BY 
                            link_autor
                    ) AS rsci_counts ON doctors.`Ссылка на Elibrary` = rsci_counts.link_autor 
                LEFT JOIN 
                    (
                        SELECT 
                        link_autor,
                        COUNT(link_autor) AS wos
                        FROM 
                            wos
                        GROUP BY 
                            link_autor
                    ) AS wos_counts ON doctors.`Ссылка на Elibrary` = wos_counts.link_autor 
                WHERE `Ссылка на Elibrary` <> '' LIMIT 10 OFFSET $OFFSET";
            $result = $conn->query($sql);
            break;
        default:
            // Обработка случая, когда значение action не соответствует ожидаемым значениям
            break;
    }
    
    // Преобразование результата в JSON
    $rows = array();
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
    
    $conn->close();
}
?>
