<?php session_start(); 
include "config.php";

if (!empty($_GET['id']) && isset($_POST)) {

    $id = $_GET['id'];
    $rank = isset($_POST['rank']) ? $_POST['rank'] : "";
    $work = isset($_POST['work']) ? $_POST['work'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $mail = isset($_POST['mail']) ? $_POST['mail'] : '';
    $applicant = isset($_POST['applicant']) ? $_POST['applicant'] : '';
    $vak = isset($_POST['vak']) ? $_POST['vak'] : '';
    $elibrary = isset($_POST['elibrary']) ? $_POST['elibrary'] : '';
    $dissernet = isset($_POST['dissernet']) ? $_POST['dissernet'] : '';
    $abstract = isset($_POST['abstract']) ? $_POST['abstract'] : '';
    $cipher = isset($_POST['cipher']) ? $_POST['cipher'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $protection = isset($_POST['protection']) ? $_POST['protection'] : '';
    $industry = isset($_POST['industry']) ? $_POST['industry'] : '';
    $order = isset($_POST['order']) ? $_POST['order'] : '';
    $solution = isset($_POST['solution']) ? $_POST['solution'] : '';
    $publications_count = isset($_POST['publications_count']) ? $_POST['publications_count'] : '';
    $autorID = isset($_POST['autorID']) ? $_POST['autorID'] : '';
    $SPIN = isset($_POST['SPIN']) ? $_POST['SPIN'] : '';
    $wos_count = isset($_POST['wos_count']) ? $_POST['wos_count'] : '';
    $vak_count = isset($_POST['vak_count']) ? $_POST['vak_count'] : '';
    $rsci_count = isset($_POST['rsci_count']) ? $_POST['rsci_count'] : '';
    $K1_count = isset($_POST['K1_count']) ? $_POST['K1_count'] : '';
    $K2_count = isset($_POST['K2_count']) ? $_POST['K2_count'] : '';
    $K3_count = isset($_POST['K3_count']) ? $_POST['K3_count'] : '';

    $sql = "UPDATE `doctors` SET ";
    $setValues = array();

    $setValues[] = "`Ученое звание Руч.` = '$rank'";
    $setValues[] = "`Место работы Руч.` = '$work'";
    $setValues[] = "`Номер телефона Руч.` = '$phone'";
    $setValues[] = "`Адрес электронной почты Руч.` = '$mail'";
    $setValues[] = "`Приемлемость аспиранта Руч.` = '$applicant'";
    $setValues[] = "`VAK Руч.` = '$vak'";
    $setValues[] = "`Elibrary Руч.` = '$elibrary'";
    $setValues[] = "`Dissernet Руч.` = '$dissernet'";
    $setValues[] = "`Автореферат Руч.` = '$abstract'";
    $setValues[] = "`Шифр научной специальности Руч.` = '$cipher'";
    $setValues[] = "`Год защиты Руч.` = '$year'";
    $setValues[] = "`Тема диссертации Руч.` = '$subject'";
    $setValues[] = "`Место защиты Руч.` = '$protection'";
    $setValues[] = "`Отрасль науки Руч.` = '$industry'";
    $setValues[] = "`Приказ Руч.` = '$order'";
    $setValues[] = "`Решение Руч.` = '$solution'";
    $setValues[] = "`Количество публикаций на Elibrary Руч.` = '$publications_count'";
    $setValues[] = "`Author ID Руч.` = '$autorID'";
    $setValues[] = "`SPIN-код Руч.` = '$SPIN'";
    $setValues[] = "`Количество публикаций на WoS Руч.` = '$wos_count'";
    $setValues[] = "`Количество публикаций на VAK Руч.` = '$vak_count'";
    $setValues[] = "`Количество публикаций на rsci Руч.` = '$rsci_count'";
    $setValues[] = "`Количество изданий категорий К-1 Руч.` = '$K1_count'";
    $setValues[] = "`Количество изданий категорий К-2 Руч.` = '$K2_count'";
    $setValues[] = "`Количество изданий категорий К-3 Руч.` = '$K3_count'";


    $sql .= implode(", ", $setValues);

    $sql .= " WHERE `id` = $id";

    // print_r($sql);
    $stmt = $conn->prepare($sql);

    if ($stmt->execute()) {
        // echo "Данные успешно обновлены";
        // header("Refresh: 0");
        
        
    } else {
        echo "Ошибка при обновлении данных: " . $conn->error;
    } 
}

if(!empty($_POST['name_publication']) && isset($_POST['name_publication']) && !isset($_GET['update']) )
{
    $name_publication = isset($_POST['name_publication']) ? $_POST['name_publication'] : "";
    $link_publication = isset($_POST['link_publication']) ? $_POST['link_publication'] : "";
    $autor_publication = isset($_POST['autor_publication']) ? $_POST['autor_publication'] : "";
    $jurnal_publication = isset($_POST['jurnal_publication']) ? $_POST['jurnal_publication'] : "";
    $year_publication = isset($_POST['year_publication']) ? $_POST['year_publication'] : "";
    $other_publication = isset($_POST['other_publication']) ? $_POST['other_publication'] : "";
    $type_publication = isset($_POST['type_publication']) ? $_POST['type_publication'] : "";

    $autor_id = isset($_POST['autor_id']) ? $_POST['autor_id'] : "";
    $sql = "";
    switch ($type_publication) {
        case 'ВАК':
            $sql = "INSERT INTO `vak`(`link_autor`, `name`, `link`, `autor`, `journal`, `more_journal`, `year`, `more`) 
                    VALUES ('$autor_id', '$name_publication', '$link_publication', '$autor_publication', 
                            '$jurnal_publication', '$jurnal_publication', '$year_publication', '$other_publication')";
            break;
        case 'WOS':
            $sql = "INSERT INTO `wos`(`link_autor`, `name`, `link`, `autor`, `journal`, `more_journal`, `year`, `more`) 
                    VALUES ('$autor_id', '$name_publication', '$link_publication', '$autor_publication', 
                            '$jurnal_publication', '$jurnal_publication', '$year_publication', '$other_publication')";
            break;
        case 'RSCI':
            $sql = "INSERT INTO `rsci`(`link_autor`, `name`, `link`, `autor`, `journal`, `more_journal`, `year`, `more`) 
                    VALUES ('$autor_id', '$name_publication', '$link_publication', '$autor_publication', 
                            '$jurnal_publication', '$jurnal_publication', '$year_publication', '$other_publication')";
            break;
        default:
            // Обработка неверного типа публикации
            break;
    }
    // Выполняем SQL-запрос
    if (!empty($sql)) {
        if ($conn->query($sql) === TRUE) {
            // echo "Новая запись успешно добавлена";
        } else {
            // echo "Ошибка: " . $sql . "<br>" . $conn->error;
        }
    }
    
}
else if (!empty($_POST['name_publication']) && isset($_POST['name_publication']) && isset($_GET['update']) && isset($_GET['pub_id'])) {
    $name_publication = isset($_POST['name_publication']) ? $_POST['name_publication'] : "";
    $link_publication = isset($_POST['link_publication']) ? $_POST['link_publication'] : "";
    $autor_publication = isset($_POST['autor_publication']) ? $_POST['autor_publication'] : "";
    $jurnal_publication = isset($_POST['jurnal_publication']) ? $_POST['jurnal_publication'] : "";
    $year_publication = isset($_POST['year_publication']) ? $_POST['year_publication'] : "";
    $other_publication = isset($_POST['other_publication']) ? $_POST['other_publication'] : "";
    $type_publication = isset($_POST['type_publication']) ? $_POST['type_publication'] : "";
    $autor_id = isset($_POST['autor_id']) ? $_POST['autor_id'] : "";
    $sql = "";
     // print($type_publication);
    switch ($type_publication) {
        case 'ВАК':
            $sql = "UPDATE `vak` SET `link_autor` = '$autor_id', `name` = '$name_publication', `link` = '$link_publication', `autor` = '$autor_publication', `journal` = '$jurnal_publication', `more_journal` = '$jurnal_publication', `year` = '$year_publication', `more` = '$other_publication'  WHERE id = {$_GET['pub_id']}";
            break;
        case 'WOS':
            $sql = "UPDATE `wos` SET `link_autor` = '$autor_id', `name` = '$name_publication', `link` = '$link_publication', `autor` = '$autor_publication', `journal` = '$jurnal_publication', `more_journal` = '$jurnal_publication', `year` = '$year_publication', `more` = '$other_publication'  WHERE id = {$_GET['pub_id']}";
            break;
        case 'RSCI':
            $sql = "UPDATE `rsci` SET `link_autor` = '$autor_id', `name` = '$name_publication', `link` = '$link_publication', `autor` = '$autor_publication', `journal` = '$jurnal_publication', `more_journal` = '$jurnal_publication', `year` = '$year_publication', `more` = '$other_publication'  WHERE id = {$_GET['pub_id']}";
            break;
        default:
            // Обработка неверного типа публикации
            break;
    }
    // Выполняем SQL-запрос
    if (!empty($sql)) {
        if ($conn->query($sql) === TRUE) {
            // echo "Новая запись успешно добавлена";
        } else {
            // echo "Ошибка: " . $sql . "<br>" . $conn->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Таблица претендентов</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    </head>
    <body>
        <style type="text/css">
        .bg-dark-blue {background: #0a2b78;}
        header a {color: #fff;}
            header a:hover {color: #e4e4e4; text-decoration: none;}
    </style>
    <header>
            <div class="bg-dark-blue">
                <div class="container py-3 text-white d-flex justify-content-between align-items-center">
                    <h3><a href="index.php">ИС «Доктора наук»</a></h3>
                    <div>
                        <nav class="navbar navbar-expand-lg">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="docktors.php">
                                            Таблица
                                        </a>
                                    </li>
                                    <?php 
                                    if(isset($_SESSION['admin'])){
                                        if($_SESSION['admin']){?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="modules.php">
                                            Агрегация
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="setting.php">
                                            Настройки
                                        </a>
                                    </li>
                                    <?php  }}?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="text-center">
                        <p><?php if(isset($_SESSION['fio'])) echo "{$_SESSION['fio']}"; ?></p>
                        <a href="destroy.php" class="btn btn-primary">Выйти</a>
                    </div>
                </div> 
            </div>
        </header>
        <style>
            .sticky-left {
                position: sticky;
                left: 0;
                background-color: #f8f9fa; /* Цвет фона первого столбца */
                z-index: 1;
            }
            .bg-red {background: #fdc6c6;}
            .bg-yellow {background: #f7f0b3;}
        </style>
        <div>
            <?php
$yearStart = $conn->query("SELECT `start_year` FROM `setting`")->fetch_assoc()['start_year'];
$year = $conn->query("SELECT `end_year` FROM `setting`")->fetch_assoc()['end_year']; //date('Y');

            // Обработка параметра id в URL для фильтрации данных
            $id = isset($_GET['id']) ? $_GET['id'] : 1;
            $id_autor = NULL;
            // Выборка данных с учетом фильтрации по первой букве фамилии автора
            if (!empty($id)) {
                $sql = "SELECT 
    doctors.*,
    COALESCE(vak_counts.VAK, 0) AS vak,
    COALESCE(rsci_counts.rsci, 0) AS rsci,
    COALESCE(wos_counts.wos, 0) AS wos,
    COALESCE(vak_counts.K1, 0) AS K1,
    COALESCE(vak_counts.K2, 0) AS K2,
    COALESCE(vak_counts.K3, 0) AS K3
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
                WHERE `id` = $id";
            }

            $result = $conn->query($sql);


            // Вывод данных

            echo '<form action="" method="POST"><table class="table table-bordered m-3">
                        <tbody>';

            if ($result->num_rows > 0) {
                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {

                    $a_my = ($row["Свои диссертации (Dissernet)"] == 0) ? "Записи нет" : '<a href="'.$row["Ссылка на Dissernet"].'&key=1" target="_blank">Записей: '.$row["Свои диссертации (Dissernet)"].'</a>';
                    $a_alien = ($row["Чужие диссертации (Dissernet)"] == 0) ? "Записи нет" : '<a href="'.$row["Ссылка на Dissernet"].'&key=12" target="_blank">Записей: '.$row["Чужие диссертации (Dissernet)"].'</a>';
                    $a_publication = ($row["Публикации (Dissernet)"] == 0) ? "Записи нет" : '<a href="'.$row["Ссылка на Dissernet"].'&key=13" target="_blank">Записей: '.$row["Публикации (Dissernet)"].'</a>';
                    
                    $rank = ($row["Ученое звание Руч."] != NULL) ? $row["Ученое звание Руч."] : "";
                    $phone = ($row["Номер телефона Руч."] != NULL) ? $row["Номер телефона Руч."] : "";
                    $mail = ($row["Адрес электронной почты Руч."] != NULL) ? $row["Адрес электронной почты Руч."] : "";
                    $work = ($row["Место работы Руч."] != NULL) ? $row["Место работы Руч."] : $row["Место работы"];

                    $applicant = ($row["Приемлемость аспиранта Руч."] != NULL) ? $row["Приемлемость аспиранта Руч."] : $row["Претенденты ФИО"];
                    $vak = ($row["VAK Руч."] != NULL) ? $row["VAK Руч."] : $row["Ссылка на ВАК"];
                    $id_autor = $elibrary = ($row["Elibrary Руч."] != NULL) ? $row["Elibrary Руч."] : $row["Ссылка на Elibrary"];
                    $dissernet = ($row["Dissernet Руч."] != NULL) ? $row["Dissernet Руч."] : $row["Ссылка на Dissernet"];
                    $abstract = ($row["Автореферат Руч."] != NULL) ? $row["Автореферат Руч."] : $row["Авторефират"];
                    $cipher = ($row["Шифр научной специальности Руч."] != NULL) ? $row["Шифр научной специальности Руч."] : $row["Шифр научной специальности, по которой защищена диссертация"];
                    $year = ($row["Год защиты Руч."] != NULL && $row["Год защиты Руч."] != 0) ? $row["Год защиты Руч."] : date("Y", strtotime($row["Год защиты"]));
                    $subject = ($row["Тема диссертации Руч."] != NULL) ? $row["Тема диссертации Руч."] : $row["Тема диссертации"];
                    $protection = ($row["Место защиты Руч."] != NULL) ? $row["Место защиты Руч."] : $row["Место защиты"];
                    $industry = ($row["Отрасль науки Руч."] != NULL) ? $row["Отрасль науки Руч."] : $row["Отрасль науки"];
                    $order = ($row["Приказ Руч."] != NULL) ? $row["Приказ Руч."] : $row["Приказ"];
                    $solution = ($row["Решение Руч."] != NULL) ? $row["Решение Руч."] : $row["Решение"];
                    $publications_count = ($row["Количество публикаций на Elibrary Руч."] != NULL && $row["Количество публикаций на Elibrary Руч."] != 0) ? $row["Количество публикаций на Elibrary Руч."] : $row["Колличество публикаций на Elibrary"];
                    $autorID = ($row["Author ID Руч."] != NULL) ? $row["Author ID Руч."] : $row["Author ID"];
                    $SPIN = ($row["SPIN-код Руч."] != NULL) ? $row["SPIN-код Руч."] : $row["SPIN-код"];
                    $wos_count = ($row["Количество публикаций на WoS Руч."] != NULL && $row["Количество публикаций на WoS Руч."] != 0) ? $row["Количество публикаций на WoS Руч."] : $row["wos"];
                    $vak_count = ($row["Количество публикаций на VAK Руч."] != NULL && $row["Количество публикаций на VAK Руч."] != 0) ? $row["Количество публикаций на VAK Руч."] : $row["vak"];
                    $rsci_count = ($row["Количество публикаций на rsci Руч."] != NULL && $row["Количество публикаций на rsci Руч."] != 0) ? $row["Количество публикаций на rsci Руч."] : $row["rsci"];
                    $K1_count = ($row["Количество изданий категорий К-1 Руч."] != NULL && $row["Количество изданий категорий К-1 Руч."] != 0) ? $row["Количество изданий категорий К-1 Руч."] : $row["K1"];
                    $K2_count = ($row["Количество изданий категорий К-2 Руч."] != NULL && $row["Количество изданий категорий К-2 Руч."] != 0) ? $row["Количество изданий категорий К-2 Руч."] : $row["K2"];
                    $K3_count = ($row["Количество изданий категорий К-3 Руч."] != NULL && $row["Количество изданий категорий К-3 Руч."] != 0) ? $row["Количество изданий категорий К-3 Руч."] : $row["K3"];

                    $a_VAK = ($vak != NULL) ? '<a href="'.$vak.'" target="_blank">VAK</a><br>' : "";
                    $a_Elibrary = ($elibrary != NULL) ? '<a href="'.$elibrary.'" target="_blank">Elibrary</a><br>' : "";
                    $a_Dissernet = ($dissernet != NULL) ? '<a href="'.$dissernet.'" target="_blank">Dissernet</a><br>' : "";
                    $a_Abstract = ($abstract != NULL) ? '<a href="'.$abstract.'" target="_blank">Автореферат</a>' : "";

                     $button = '';
                     $disabled = 'disabled';
                    if(isset($_SESSION['admin'])){
                        if($_SESSION['admin']){
                           $button = ' </br><button type="submit" class="btn btn-primary my-2">Обновить данные доктора</button>';
                           $disabled = '';
                        }
                    }

                    echo '
                    <tr  class="sticky-top bg-white">
                        <th >Претендент ФИО '.$button.' </br><a href="docktorPDF.php?id='.$row['id'].'" target="_blank" class="btn btn-primary my-2">Скачать</a></th>
                        <td class="form-group">
                            <input '.$disabled.' type="text" class="form-control"  name="applicant"  value="'.$applicant.'" placeholder="Нет данных">
                            
                            <div class="my-2">
                                <div class="d-flex align-items-center justify-content-between"><div>'.$a_VAK.'</div><input '.$disabled.' type="text" class="form-control w-75"  name="vak"  value="'.$vak.'" placeholder="Нет данных по VAK"></div>
                                <div  class="d-flex align-items-center justify-content-between"><div>'.$a_Elibrary.'</div><input '.$disabled.' type="text" class="form-control w-75"  name="elibrary"  value="'.$elibrary.'" placeholder="Нет данных по Elibrary"></div>
                               <div  class="d-flex align-items-center justify-content-between"> <div>'.$a_Dissernet.'</div><input '.$disabled.' type="text" class="form-control w-75"  name="dissernet"  value="'.$dissernet. '" placeholder="Нет данных по Dissernet"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between"><div>'.$a_Abstract.'</div><input '.$disabled.' type="text" class="form-control w-75"  name="abstract"  value="'.$abstract. '" placeholder="Нет данных по Автореферату"></div>
                            
                        </td>
                    </tr>
                    <tr><th>Шифр научной специальности, по которой защищена диссертация</th>
                        <td class="form-group">
                            <input '.$disabled.' type="text" class="form-control"  name="cipher"  value="'.$cipher.'" placeholder="Нет данных">
                        </td>   
                     </tr>
                     <tr>  <th>Ученое звание</th> 
                        <td class="form-group">
                            <input '.$disabled.' type="text" class="form-control"  name="rank"  value="'.$rank.'" placeholder="Нет данных">
                        </td>     
                     </tr>
                    <tr>   <th>Год защиты диссертации</th>
                        <td class="form-group">
                        <input '.$disabled.' type="text" class="form-control"  name="year"  value="'.$year.'" placeholder="Нет данных">
                        </td>
                        
                     </tr>
                    <tr>  <th>Место работы</th> 
                        <td class="form-group">
                            <input '.$disabled.' type="text" class="form-control"  name="work"  value="'.$work.'" placeholder="Нет данных">
                        </td>     
                     </tr>
                    <tr>   <th>Тема диссертации</th>
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="subject"  value="'.$subject.'" placeholder="Нет данных"></td>
                        
                    </tr>
                    <tr>  
                    <th>Место защиты</th>
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="protection"  value="'.$protection.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr> <th>Отрасль науки</th>   
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="industry"  value="'.$industry.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr>  <th>Приказ</th> 
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="order"  value="'.$order.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr> <th>Решение</th>   
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="solution"  value="'.$solution.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr>  <th>Количество публикаций на Elibrary</th>  
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="publications_count"  value="'.$publications_count.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr> <th>Author ID</th>   
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="autorID"  value="'.$autorID.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr> <th>SPIN-код</th>   
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="SPIN"  value="'.$SPIN.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr> <th>WoS, Scopus (не менее 2)</th>   
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="wos_count"  value="'.$wos_count.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr> <th>ВАК (не менее 5)</th>   
                        <td class="form-group"><input '.$disabled.' type="text" class="form-control"  name="vak_count"  value="'.$vak_count.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr>  <th>Издания категорий К-1 и К-2, RSCI, Q1 и Q2. (не менее 9)</th>  
                        <td class="form-group">RSCI - <input '.$disabled.' type="text" class="form-control"  name="rsci_count"  value="'.$rsci_count.'" placeholder="Нет данных"> <br> K1 - <input '.$disabled.' type="text" class="form-control"  name="K1_count"  value="'.$K1_count.'" placeholder="Нет данных"> <br> K2 - <input '.$disabled.' type="text" class="form-control"  name="K2_count"  value="'.$K2_count.'" placeholder="Нет данных"> <br> K3 - <input '.$disabled.' type="text" class="form-control"  name="K3_count"  value="'.$K3_count.'" placeholder="Нет данных"></td>
                    </tr>
                    <tr><th>    Диссертации претендента</th>    
                        <td>'.$a_my.'</td>
                    </tr>
                    <tr> <th>Претендент является оппонентом/руководителем/консультантом</th>   
                        <td>'.$a_alien.'</td>
                    </tr>
                    <tr> <th>Публикации претендента</th>  
                        <td>'.$a_publication.'</td>
                    </tr>
                    <tr> <th>Номер телефона</th>  
                        <td class="form-group">
                            <input '.$disabled.' type="text" class="form-control"  name="phone"  value="'.$phone.'" placeholder="Нет данных">
                        </td>
                    </tr>
                    <tr> <th>Адрес электронной почты</th>  
                        <td class="form-group">
                            <input '.$disabled.' type="text" class="form-control"  name="mail"  value="'.$mail.'" placeholder="Нет данных">
                        </td>
                    </tr>
                    ';
                }
            } else {
                echo'
                    <tr>
                        <td colspan="20">Нет результатов</td>
                        
                    </tr>
                    ';
            }

            echo '</tbody>
                </table>
               
            </form>';

            ?>

            <?php

            if(isset($_SESSION['admin'])){
                if($_SESSION['admin']){
            echo '<form action="" method="POST" class="m-3">
                <h3>Добавить публикацию</h3>
                <table class="table  w-100">
                    <tr>
                        <th>Название публикации</th>
                        <th>Ссылка на публикацию</th>
                        <th>Авторы</th>
                        <th>Журнал</th>
                        <th>Год</th>
                        <th>Примечание</th>
                        <th>ВАК/WOS/RSCI</th>
                    </tr>
                    <tr>
                        <td><textarea class="form-control" name="name_publication" placeholder="ПРИОРИТЕТЫ И ГЛАВНЫЕ ИНСТРУМЕНТЫ РАЗВИТИЯ ЦИФРОВОЙ ЭКОНОМИКИ РОССИИ"></textarea>
                            <input type="hidden" name="autor_id" value="'.$id_autor.'"></td>
                        <td><textarea class="form-control" name="link_publication" placeholder="https://www.elibrary.ru/item.asp?id=23642363"></textarea></td>
                        <td><textarea class="form-control" name="autor_publication" placeholder="Иванов И.И., Петров Д.И."></textarea></td>
                        <td><textarea  class="form-control" name="jurnal_publication" placeholder="Организатор производства"></textarea></td>
                        <td><input class="form-control" type="number" name="year_publication" placeholder="2024"></td>
                        <td><textarea class="form-control" name="other_publication" placeholder="№ 1 (38). С. 3-11."></textarea></td>
                        <td>
                            <select name="type_publication" class="form-control">
                                <option value="ВАК">ВАК</option>
                                <option value="WOS">WOS</option>
                                <option value="RSCI">RSCI</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="text-center my-2">
                    <button type="submit" class="btn btn-primary">Добавить публикацию</button>
                </div>
                
            </form>';
            if (isset($_GET['pub_base']) && isset($_GET['pub_id']) && isset($_GET['update'])) {
                 $pub_base = $_GET['pub_base'];
                    $pub_id = $_GET['pub_id'];

                    // Подготавливаем SQL-запрос с использованием подстановки значений
                    $sql = "SELECT * FROM $pub_base WHERE id = $pub_id";

                $result = $conn->query($sql);
                $resurs = ($_GET['pub_base'] == "vak") ? "ВАК" : (($_GET['pub_base'] == "wos") ? "WOS" : "RSCI");

                // Проверка наличия результатов
                if ($result->num_rows > 0) {
                    // Вывод данных каждой строки
                    while($row = $result->fetch_assoc()) {
                        echo '<form action="" method="POST" class="m-3 d-none " id="form_update">
                            <h3>Обновить публикацию</h3>
                            <table class="table  w-100">
                                <tr>
                                    <th>Название публикации</th>
                                    <th>Ссылка на публикацию</th>
                                    <th>Авторы</th>
                                    <th>Журнал</th>
                                    <th>Год</th>
                                    <th>Примечание</th>
                                    <th>ВАК/WOS/RSCI</th>
                                </tr>
                                <tr>
                                    <input type="hidden" name="autor_id" value="'.$id_autor.'"></td>
                                    <td><textarea class="form-control" name="name_publication">' . $row['name'] . '</textarea></td>
                                    <td><textarea class="form-control" name="link_publication">' . $row['link'] . '</textarea></td>
                                    <td><textarea class="form-control" name="autor_publication">' . $row['autor'] . '</textarea></td>
                                    <td><textarea class="form-control" name="jurnal_publication">' . $row['journal'] . '</textarea></td>
                                    <td><input class="form-control" type="number" name="year_publication" value="' . $row['year'] . '"></td>
                                    <td><textarea class="form-control" name="other_publication">' . $row['more'] . '</textarea></td>

                                    <td>
                                        <select name="type_publication" class="form-control">
                                             <option value="ВАК"' . ($resurs === "ВАК" ? " selected" : "") . '>ВАК</option>
                                                <option value="WOS"' . ($resurs === "WOS" ? " selected" : "") . '>WOS</option>
                                                <option value="RSCI"' . ($resurs === "RSCI" ? " selected" : "") . '>RSCI</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <div class="text-center my-2">
                                <button type="submit" class="btn btn-primary">Обновить публикацию</button>
                                <a href="?id='.$_GET['id'].'" class="btn btn-primary">Отмена</a>
                            </div>
                            
                        </form>';
                    }
                }
            

            }}}
            if ($id_autor != NULL)  {
            echo '<h1 class="m-3">Публикации:</h1>';
        
            
                $sql = "SELECT vak.id AS vak_id, vak.*, vakcategory2023.*  FROM vak LEFT JOIN vakcategory2023 ON vak.journal = vakcategory2023.Name WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
                $result = $conn->query($sql);
                // Вывод данных

            echo '<table class="table table-bordered m-3">
                <thead>
                <tr>
                                <th colspan="6">ВАК</th>
                            </tr>
                            <tr>
                                <th >Публикация</th>
                                <th >Авторы</th>
                                <th >Журнал</th>
                                <th >Год</th>
                                <th >Прочее</th>
                                <th >Категория</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($result->num_rows > 0) {
                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <tr class="'. (($row["year"] < $yearStart) ? "bg-red" : "bg-white") .'">
                        <td><a href="'.$row["link"].'" target="_blank">'.$row["name"].'</a></td>
                        <td>'.$row["autor"].'</td>
                        <td>'.$row["journal"].'</td>
                        <td>'.$row["year"].'</td>
                        <td>'.$row["more"].'</td>
                        <td>'.$row["Category"].'</td>
                        <td><a href="?id='.$_GET['id'].'&update&pub_id='.$row["vak_id"].'&pub_base=vak" class="btn btn-info w-100">Изменить</a><br/><a href="delite.php?id='.$_GET['id'].'&pub_id='.$row["vak_id"].'&pub_base=vak" class="btn btn-danger w-100">Удалить</a></td>
                    </tr>
                    ';
                }
            } else {
                echo'
                    <tr>
                        <td colspan="6">Нет результатов</td>
                        
                    </tr>
                    ';
            }

            echo '</tbody>';
                $sql = "SELECT * FROM `vak` WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
                $vak_result = $conn->query($sql);
                if ($vak_result->num_rows > 0) {
                    while ($row = $vak_result->fetch_assoc()) {
                        $vak_results[] = $row;
                    }
                }
                $sql = "SELECT * FROM `wos` WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
                $result = $conn->query($sql);
echo '<thead>
                            <tr>
                                <th colspan="5">WOS</th>
                            </tr>
                            <tr>
                                <th >Публикация</th>
                                <th >Авторы</th>
                                <th >Журнал</th>
                                <th >Год</th>
                                <th >Прочее</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($result->num_rows > 0) {

                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {
                    $row_class = "bg-white";
                    foreach ($vak_results as $vak_row) {
                        $row_class = (($vak_row["link"] == $row["link"] || 'http://localhost/doctor/'.$vak_row["link"] == $row["link"]) && $vak_row["name"] == $row["name"]) ? "bg-yellow" : $row_class;
                    }
                    $row_class = ($row["year"] < $yearStart) ? "bg-red" : $row_class;
                    echo '
                    <tr class="' . $row_class . '">
                        <td><a href="'.$row["link"].'" target="_blank">'.$row["name"].'</a></td>
                        <td>'.$row["autor"].'</td>
                        <td>'.$row["journal"].'</td>
                        <td>'.$row["year"].'</td>
                        <td>'.$row["more"].'</td>
                        <td><a href="?id='.$_GET['id'].'&update&pub_id='.$row["id"].'&pub_base=wos" class="btn btn-info w-100">Изменить</a><br/><a href="delite.php?id='.$_GET['id'].'&pub_id='.$row["id"].'&pub_base=wos" class="btn btn-danger w-100">Удалить</a></td>
                    </tr>
                    ';
                }
            } else {
                echo'
                    <tr>
                        <td colspan="5">Нет результатов</td>
                        
                    </tr>
                    ';
            }

            echo '</tbody>';
                $sql = "SELECT * FROM `vak` WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
                $vak_result = $conn->query($sql);
                if ($vak_result->num_rows > 0) {
                    while ($row = $vak_result->fetch_assoc()) {
                        $vak_results[] = $row;
                    }
                }
                $sql = "SELECT * FROM `rsci` WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
                $result = $conn->query($sql);
echo '<thead><tr>
                                <th colspan="5">RSCI</th>
                            </tr>
                            <tr>
                                <th >Публикация</th>
                                <th >Авторы</th>
                                <th >Журнал</th>
                                <th >Год</th>
                                <th >Прочее</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($result->num_rows > 0) {
                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {
                    $row_class = "bg-white";
                    foreach ($vak_results as $vak_row) {
                        $row_class = (($vak_row["link"] == $row["link"] || 'http://localhost/doctor/'.$vak_row["link"] == $row["link"]) && $vak_row["name"] == $row["name"]) ? "bg-yellow" : $row_class;
                    }
                    $row_class = ($row["year"] < $yearStart) ? "bg-red" : $row_class;
                    echo '
                    <tr class="' . $row_class . '">
                        <td><a href="'.$row["link"].'" target="_blank">'.$row["name"].'</a></td>
                        <td>'.$row["autor"].'</td>
                        <td>'.$row["journal"].'</td>
                        <td>'.$row["year"].'</td>
                        <td>'.$row["more"].'</td>
                        <td><a href="?id='.$_GET['id'].'&update&pub_id='.$row["id"].'&pub_base=rsci" class="btn btn-info w-100">Изменить</a><br/><a href="delite.php?id='.$_GET['id'].'&pub_id='.$row["id"].'&pub_base=rsci" class="btn btn-danger w-100">Удалить</a></td>
                    </tr>
                    ';
                }
            } else {
                echo'
                    <tr>
                        <td colspan="5">Нет результатов</td>
                        
                    </tr>
                    ';
            }

            echo '</tbody>
                </table>';
            } 
            // Закрываем соединение
            $conn->close();
            ?>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            // Проверяем наличие параметра 'update' в URL
            if (window.location.href.indexOf('update') > -1) {
                // Найдем элемент, к которому нужно прокрутить
                var element = document.getElementById('form_update'); // Замените 'element_id' на ID нужного вам элемента
                element.classList.remove('d-none');
                // Если элемент найден, выполним прокрутку к нему
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        </script>
        
    </body>
</html>
