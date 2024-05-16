<?php session_start(); 
include "config.php";
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
        <?php
        // Функции

            // Функция для разделения строки по разделителю " - "
            function splitSpecialty($specialty) {
                $specialty = str_replace('–', '-', $specialty);
                return explode("  ", $specialty, 2);
            }

        // Переменные

            $minWOS = 2;
            $minVAK = 5;
            $minRSCI = 9;

            // Получаем код или название специальности для отображения в выборе
            $filter_specialty = isset($_GET['filter_specialty']) ? $_GET['filter_specialty'] : NULL;

            // Получаем ФИО доктара для отображения в таблице
            $nameFilter = isset($_GET['name']) ? $_GET['name'] : NULL;

            // Получаем выбранные специальности для отображения в таблице
            $specialties = isset($_GET['specialty']) ? $_GET['specialty'] : array();
            
            // Разделяем каждый элемент массива
            $splitSpecialties = array_map('splitSpecialty', $specialties);

            // Получаем номер текущей страницы
            $pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            // Колличество выводиммых данных на страницу
            $pageSize = 100;

            // Вычисляем смещение (OFFSET) на основе номера страницы и количества записей на странице
            $offset = ($pageNumber - 1) * $pageSize;

            $where = '';

            $specialtyFilter = [];

            // Стартовый год для Публикаций
            $yearStart = $conn->query("SELECT `start_year` FROM `setting`")->fetch_assoc()['start_year'];

            // Текущий год для Публикаций
            $year = $conn->query("SELECT `end_year` FROM `setting`")->fetch_assoc()['end_year'];

        // Функционал

            // Заполняем условие для фильтрации данных. Фильтр ФИО доктора
            if (!empty($nameFilter)) {
                $where .= "`Претенденты ФИО` LIKE '%$nameFilter%'";
            }

            // Заполняем условие для фильтрации данных. Фильтр специальность
            if (!empty($splitSpecialties)) {
                if(!empty($where)) $where .= " AND ";
                $where .= "(";
                $isFirstIteration = true;
                foreach ($splitSpecialties as $specialty) {
                    $code1 = trim(explode(" - ", $specialty[0])[0]);
                    $code2 = trim(explode(" - ", $specialty[1])[0]);
                    $specialtyFilter[] = $code1;
                    $specialtyFilter[] = $code2;
                    if (!$isFirstIteration) {
                        $where .= " OR ";
                    } else {
                        $isFirstIteration = false;
                    }
                    $where .= " `Шифр научной специальности, по которой защищена диссертация` LIKE '%$code1%' OR `Шифр научной специальности, по которой защищена диссертация` LIKE '%$code2%'";
                }
                $where .= ")";
            }

            // Получение списка уникальных букв фамилий авторов
            $sqlLetters = "SELECT DISTINCT LEFT(`Претенденты ФИО`, 1) AS first_letter FROM doctors ORDER BY `Претенденты ФИО`";
            $resultLetters = $conn->query($sqlLetters);
            $letters = [];
            if ($resultLetters->num_rows > 0) {
                while ($rowLetters = $resultLetters->fetch_assoc()) {
                    $letters[] = $rowLetters['first_letter'];
                }
            }

            // Обработка параметра letter в URL для фильтрации данных
            $letterFilter = isset($_GET['letter']) ? $_GET['letter'] : 'А';
            if(empty($where)) $where .= "LEFT(`Претенденты ФИО`, 1) = '$letterFilter'";


        // Выборка данных
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
                WHERE $where 
                GROUP BY `Претенденты ФИО`, `Тема диссертации`, `Год защиты`
                ORDER BY `Претенденты ФИО`";
                
            $result = $conn->query($sql);
            $countSort = $result->num_rows;
        ?>

        <style>
 /* Apply border-collapse and border-spacing to table */
table {
    border-collapse: separate;
    border-spacing: 0px 0px;
}
table thead {background: #fff}
table tbody tr {
    border-spacing: 0px 10px;
}
            .text-black {color: black;}
            .bg-dark-blue {background: #0a2b78;}
            .table td {vertical-align: middle;
            border-top: none;}
            .table thead th {border: none;}
            .table td, .table th {border: 1px solid #e7e7e7 !important;}
            th {
                min-width: 235px;}
            .sticky-top {top: -1px !important;}
            .sticky-left {
                position: sticky;
                left: 0;
                background-color: #fff;
                z-index: 1;
            }
            .sticky-left-specialty {
            position: sticky;
            left: 139px; /* Зависит от ширины первого столбца */
            background-color: #fff;
            z-index: 1;
            }
            .bg-red {background: #fdc6c6;}
            .bg-yellow {background: #f7f0b3;}
            
        #popup {
            position: fixed;
            bottom: 0;
            top: 0;
            background-color: #ffffff;
            z-index: 1050;
            right: 0;
            overflow: auto;
        }
     
            .dropdown:hover .dropdown-menu {
                display: block;
            }




.main {
    overflow-y: scroll;
    height: calc(100vh - 104px);
}

.table-header {
    position: sticky;
    top: 0;
    z-index: 2;
    background-color: #fff; /* Adjust as needed */
}
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
            <div class="container py-3 d-flex justify-content-between align-items-center">
                <a href="getDoctorsOnFile.php" class="btn btn-primary">Скачать таблицу в Excel</a>
                <div class="text-center">
                    <p class='m-0'>Страницы и статистика:</p>
                    <?php 
                    // Подсчитываем общее количество записей
                        $totalRecords = $conn->query("SELECT `Претенденты ФИО` FROM doctors GROUP BY `Претенденты ФИО`, `Тема диссертации`, `Год защиты`;")->num_rows;
                        // Вычисляем общее количество страниц
                        $totalPages = ceil($totalRecords / $pageSize);

                        // for ($i = 1; $i <= $totalPages; $i++) {
                        //     echo "<a href='docktors.php?page=$i'>$i</a> ";
                        // }
                        foreach ($letters as $letter) {
                            echo "<a class='text-black' href='docktors.php?letter=$letter'>$letter</a> ";
                        }
                        echo "<p class='m-0'>Итого претендентов: $totalRecords. Записей по фильтру: $countSort</p>";
                    ?>
                </div>
                <div>
                    <button class="btn btn-primary" type="button" onclick="filter(event)">Показать фильтр</button>
                    <button onclick="scrollToTop()" class="btn btn-primary">↑</button>
                </div>
            </div>
        </header>

        <div class="main mx-3">
            <?php
            // Вывод данных

            echo '<table class="table  table-hover">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" rowspan="3" class="sticky-left">Претенденты ФИО</th>
                                <th scope="col" colspan="11" class="sticky-left">Сведения</th>
                                <th scope="col" colspan="6" class="sticky-left">Критерии выбора</th>
                            </tr>
                            <tr>
                                <th scope="col" rowspan="2" class="sticky-left-specialty">Шифр научной специальности, по которой защищена диссертация</th>
                                <th scope="col" rowspan="2">Год защиты диссертации</th>
                                <th scope="col" rowspan="2">Место работы</th>
                                <th scope="col" colspan="5">VAK</th>
                                <th scope="col" colspan="3">Elibrary</th>
                                <th scope="col" colspan="3">Публикации за '.$yearStart.'-'.$year.'</th>
                                <th scope="col" colspan="3">Диссернет</th>
                                
                            </tr>
                            <tr>
                                <th scope="col">Тема диссертации</th>
                                <th scope="col">Место защиты</th>
                                <th scope="col">Отрасль науки</th>
                                <th scope="col">Приказ</th>
                                <th scope="col">Решение</th>
                                <th scope="col">Количество публикаций на Elibrary</th>
                                <th scope="col">Author ID</th>
                                <th scope="col">SPIN-код</th>
                                <th scope="col">ВАК (не менее 5)</th>
                                <th scope="col">WoS, Scopus (не менее 2)</th>
                                
                                <th scope="col">Издания категорий К-1 и К-2, RSCI, Q1 и Q2. (не менее 9)</th>
                                <th scope="col">Диссертации претендента</th>
                                <th scope="col">Претендент является оппонентом/руководителем/консультантом </th>
                                <th scope="col">Публикации претендента</th>
                            </tr>
                        </thead>
                        <tbody>';
                        // <th scope="col">Издания, отнесенные к категории К-1 и К-2, включенные в Перечень рецензируемых научных изданий, научные издания, индексируемые в базе данных RSCI, научные издания из Q1 и Q2, индексируемые международными базами данных, перечень которых определен в соответствии с рекомендациями ВАК (не менее 9)</th>
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Обработка и вывод данных
                    $id_autor = $elibrary = ($row["Elibrary Руч."] != NULL) ? $row["Elibrary Руч."] : $row["Ссылка на Elibrary"];
                    $a_my = ($row["Свои диссертации (Dissernet)"] == 0) ? "Записи нет" : '<a href="'.$row["Ссылка на Dissernet"].'&key=1" target="_blank">Записей: '.$row["Свои диссертации (Dissernet)"].'</a>';
                    $a_alien = ($row["Чужие диссертации (Dissernet)"] == 0) ? "Записи нет" : '<a href="'.$row["Ссылка на Dissernet"].'&key=12" target="_blank">Записей: '.$row["Чужие диссертации (Dissernet)"].'</a>';
                    $a_publication = ($row["Публикации (Dissernet)"] == 0) ? "Записи нет" : '<a href="'.$row["Ссылка на Dissernet"].'&key=13" target="_blank">Записей: '.$row["Публикации (Dissernet)"].'</a>';
                    $a_VAK = ($row["Ссылка на ВАК"] != NULL) ? '<a href="'.$row["Ссылка на ВАК"].'" target="_blank">VAK</a><br>' : "";
                    $a_Elibrary = ($elibrary != NULL) ? '<a href="'.$elibrary.'" target="_blank">Elibrary</a><br>' : "";
                    $a_Dissernet = ($row["Ссылка на Dissernet"] != NULL) ? '<a href="'.$row["Ссылка на Dissernet"].'" target="_blank">Dissernet</a><br>' : "";
                    $a_Abstract = ($row["Авторефират"] != NULL) ? '<a href="'.$row["Авторефират"].'" target="_blank">Автореферат</a>' : "";
                    $irrelevant = "bg-white";
                    $irrelevant = ($row["wos"] <= $minWOS || $row["vak"] <= $minVAK || ($row["K1"] + $row["K2"]) <= $minRSCI) ? "bg-yellow" : $irrelevant;
                    $irrelevant = ($row["Свои диссертации (Dissernet)"] > 0) || ($row["Чужие диссертации (Dissernet)"] > 0) || ($row["Публикации (Dissernet)"] > 0) ? "bg-red" : $irrelevant;
                    
                    echo '
                    <tr class="'.$irrelevant.'">
                        <td class="sticky-left">
                            <a href="docktor.php?id='.$row["id"].'" target="_blank">'.$row["Претенденты ФИО"].'</a>
                            <div class="my-2">
                                '.$a_VAK.$a_Elibrary.$a_Dissernet. '
                            </div>
                            '.$a_Abstract. '
                            <a href="docktorPDF.php?id='.$row['id'].'" target="_blank" class="btn btn-primary my-2">Скачать карточку</a>
                        </td>
                        <td class="sticky-left-specialty">'.$row["Шифр научной специальности, по которой защищена диссертация"].'</td>
                        <td>'. date("Y", strtotime($row["Год защиты"])).'</td>
                        <td>'.$row["Место работы"].'</td>
                        <td>'.$row["Тема диссертации"].'</td>
                        <td>'.$row["Место защиты"].'</td>
                        <td>'.$row["Отрасль науки"].'</td>
                        <td>'.$row["Приказ"].'</td>
                        <td>'.$row["Решение"].'</td>
                        <td>'.$row["Колличество публикаций на Elibrary"].'</td>
                        <td>'.$row["Author ID"].'</td>
                        <td>'.$row["SPIN-код"].'</td>
                        <td>'.$row["vak"].'</td>
                        <td>'.$row["wos"].'</td>
                        
                        <td>
                            K1+K2: '.($row["K1"] + $row["K2"]).' <br><br>
                            RSCI - '.$row["rsci"].' <br>
                            K1 - '.$row["K1"].' <br>
                            K2 - '.$row["K2"].' <br>
                            K3 - '.$row["K3"].'
                            
                        </td>
                        <td>'.$a_my.'</td>
                        <td>'.$a_alien.'</td>
                        <td>'.$a_publication.'</td>
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
                </table>';

            ?>
        
        </div>


        <style type="text/css">
            .tab div {padding: 10px}
        </style>
        <div class="d-none">
            <?php print_r($where); ?>
        </div>
             <div id="popup" class="d-none w-50 p-4">
                  
                       <form id="filter" action="" method="GET" class="d-none">
                        <div class="d-flex justify-content-between">
                            <h1>Фильтры:</h1>
                            <button class="btn btn-primary" type="button" onclick="filter(event)">Скрыть фильтр</button>
                        </div>
              <?php
if (!empty($_GET['name']) || !empty($_GET['specialty']) || !empty($_GET['filter_specialty'])) {
    // Создаём контейнер для списка фильтров
    echo '<div class="applied-filters">';
    // Если указано имя, добавляем фильтр "Имя"
    if (!empty($_GET['name'])) {
        echo '<span class="badge">ФИО: </span>';
        // Создаем копию $_GET
            $newGetParams = $_GET;
            // Удаляем текущую специальность из копии
            unset($newGetParams['name']);
            // Формируем URL с обновленными параметрами
            $newUrl = 'docktors.php?' . http_build_query($newGetParams);
        echo '</br><span class="badge badge-pill badge-primary" style="font-size : 14px">' . $_GET['name'] . ' <a href="' . $newUrl . '" style="color : white">&times;</a></span>';
    }
    // Если указана специальность, добавляем фильтр "Специальность"
    if (!empty($_GET['specialty'])) {
        echo '</br><span class="badge">Специальность: </span>';
        // Проходимся по каждой специальности и выводим её в отдельной ячейке
        foreach ($_GET['specialty'] as $specialty) {
            // Создаем копию $_GET
            $newGetParams = $_GET;
            // Удаляем текущую специальность из копии
            unset($newGetParams['specialty'][array_search($specialty, $_GET['specialty'])]);
            // Формируем URL с обновленными параметрами
            $newUrl = 'docktors.php?' . http_build_query($newGetParams);
            // Выводим ссылку с обновленным URL
            echo '</br><span class="badge badge-pill badge-primary" style="font-size : 14px">' . $specialty . ' <a href="' . $newUrl . '" style="color : white">&times;</a></span>';
        }
    }
    // Закрываем контейнер для списка фильтров
    echo '</div>';
}
?>
    
    <?php
    // Преобразуем массив specialty в строку и разделяем запятыми
    $specialtyString = isset($_GET['specialty']) ? implode(',', $_GET['specialty']) : '';
    ?>

    <div class="form-group">
        <label for="nameFilter">Поиск по имени:</label>
        <input class="form-control" placeholder="Иванов Иван Иванович"  type="text" id="nameFilter" name="name">
    </div>
                        



                       <!--  <div class="form-group">
                            <label for="specialtyFilter">Фильтр по специальности:</label>
                            <div class="d-flex">
                                <input class="form-control" placeholder="5.6.1 или Отечественная" type="text" name="filter_specialty" value="<?php echo $filter_specialty?>">
                                <button id="" class="btn btn-primary">Применить</button>
                            </div>
                            <select class="form-control" id="specialtyFilter" name="specialty[]" multiple style="height: 170px;">
                                <?php
                                // Выполняем запрос к базе данных для получения списка специальностей
                                // $sqlSpecialties = "SELECT OldCipher AS name FROM specialties UNION ALL SELECT NewCipher AS name FROM specialties";
                                if(isset($filter_specialty))
                                {
                                    $sqlSpecialties = "SELECT CONCAT(NewCipher, '  ', OldCipher) AS name FROM specialties WHERE `OldCipher` LIKE '%$filter_specialty%' OR `NewCipher` LIKE '%$filter_specialty%'";
                                }
                                else $sqlSpecialties = "SELECT CONCAT(NewCipher, '  ', OldCipher) AS name FROM specialties";
                                
                                $resultSpecialties = $conn->query($sqlSpecialties);
                                // Выводим варианты выбора в виде опций выпадающего списка
                                if ($resultSpecialties->num_rows > 0) {
                                    
                                    echo  '<option value="">Все</option>';
                                    while ($rowSpecialty = $resultSpecialties->fetch_assoc()) {
                                        $Specialty = str_replace('–', '-', $rowSpecialty['name']); 
                                        list($part1, $part2) = explode('  ', $Specialty, 2);

                                        // Получаем коды специальностей из каждой части
                                        list($code1, $name1) = explode(' - ', $part1);
                                        list($code2, $name2) = explode(' - ', $part2);

                                        $selected = (in_array($code1, $specialtyFilter) || in_array($code2, $specialtyFilter)) ? 'selected' : ''; // Помечаем выбранную специальность

                                        $text = $rowSpecialty['name'];

                                        $text = str_replace('–', '-', $text);
                                        // Разделяем текст на две части
                                        list($part1, $part2) = explode('  ', $text, 2);
                                        if (!empty($part1)) {
                                            // Получаем коды и сравниваем названия
                                            list($code1, $name1) = explode(' - ', $part1);

                                            list($code2, $name2) = explode(' - ', $part2);

                                            if ($name1 == $name2) {
                                                $result = "$code1 ($code2) – $name1";
                                            } else {
                                                $result = "$code1 – $name1 ($code2 - $name2)";
                                            }
                                        } else {
                                            list($code2, $name2) = explode(' - ', $part2);
                                            $result = "$code2 – $name2";
                                        }
                                        echo '<option value="' . $rowSpecialty['name'] . '" ' . $selected . '>' . $result . '</option>';
                                    }
                                } else  echo '<option value="">Список специальностей пуст</option>';
                                
                                ?>
                            </select>
                        </div> -->

                        <div class="form-group">
        <label for="specialtyFilter">Фильтр по специальности:</label>
        <div class="d-flex">
            <input class="form-control" placeholder="5.6.1 или Отечественная" type="text" name="filter_specialty" id="filter_specialty">
        </div>
        <select class="form-control" id="specialtyFilter" name="specialty[]" multiple style="height: 460px;">
            <!-- Опции специальностей будут добавлены динамически с помощью JavaScript -->
        </select>
    </div>
                        <button type="submit" onclick="goFilter(event)" class="btn btn-primary">Применить фильтр</button>
                    </form>
                    <script>
                        // Функция для сброса значений полей формы
                        function resetForm() {
                            document.getElementById('filter_specialty').value = ''; // Очищаем поле ввода для фильтрации по специальности
                            // Если у вас есть другие поля формы, которые нужно очистить, добавьте их сюда
                        }
                    </script>
                    <form id="login" action="login.php" method="POST" class="d-none">
                        <h1>Авторизация</h1>
                        <div class="form-group">
                            <label for="login">Имя пользователя*:</label>
                            <input type="text" class="form-control" id="login" name="login" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль*:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Войти</button>
                        <a href='?registration' class="d-block">Пройти регистрацию</a>
                    </form>
                    <form id="register" action="register.php" method="POST" class="d-none">
                        <h1>Регистрация</h1>
                        <div class="form-group">
                            <label for="fio">ФИО*:</label>
                            <input type="text" class="form-control" id="fio" name="fio" required>
                        </div>
                        <div class="form-group">
                            <label for="mail">E-mail*:</label>
                            <input type="mail" class="form-control" id="mail" name="mail" required>
                        </div>
                        <div class="form-group">
                            <label for="login">Логин* (для входа):</label>
                            <input type="text" class="form-control" id="login" name="login" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль* (для входа):</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                        <a href='index.php' class="d-block">Пройти авторизацию</a>
                    </form>
                </div>
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        
        <script>
            function resetFilters() {
                document.getElementById('nameFilter').value = '';
                document.getElementById('specialtyFilter').selectedIndex = 0;
                // Дополнительные действия для сброса фильтров, если необходимо
                // Например, отправка формы после сброса фильтров
                // document.querySelector('form').submit();
            }
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Get all dropdown toggles
                var dropdownToggles = document.querySelectorAll('.dropdown-toggle');

                // Add click event listener to each dropdown toggle
                dropdownToggles.forEach(function(toggle) {
                    toggle.addEventListener('click', function(event) {
                        // Prevent default behavior (opening the dropdown on click)
                        event.preventDefault();
                        // Get the href attribute value
                        var href = this.getAttribute('href');
                        // If the href is not empty, navigate to the link
                        if (href && href !== '#') {
                            window.location.href = href;
                        }
                    });
                });
            });
        </script>
        <script>
            // Функция для скрытия/раскрытия формы
               function filter(event) {
                var form = document.getElementById("filter");
                var computedStyle = window.getComputedStyle(form);
                if (computedStyle.display === "none" || computedStyle.display === "") {
                    form.classList.remove("d-none");
                    form.parentElement.classList.remove("d-none");
                    $('header, .main').css('filter', 'blur(5px)');
                } else {
                    form.classList.add("d-none");
                    form.parentElement.classList.add("d-none");
                    $('header, .main').css('filter', 'blur(0px)');
                }
                event.preventDefault();
            }

            function registration() {
                var form = document.getElementById("register");
                    form.classList.remove("d-none");
                    form.parentElement.classList.remove("d-none");
                    $('header, .main').css('filter', 'blur(5px)');
            }

            function authorization() {
                var form = document.getElementById("login");
                    form.classList.remove("d-none");
                    form.parentElement.classList.remove("d-none");
                    $('header, .main').css('filter', 'blur(5px)');
            }

            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        </script>
        <script type="text/javascript">
            $(document).ready(() => {
                // Функция для обновления списка специальностей
                function updateSpecialties() {
                    // Получаем значение фильтра
                    const filter_specialty = $('#filter_specialty').val();

                    // Выполняем AJAX запрос для получения списка специальностей
                    $.ajax({
                        type: 'GET',
                        url: 'get_specialties.php', // Укажите путь к скрипту, который будет обрабатывать запрос и возвращать данные
                        data: {
                            filter_specialty: filter_specialty
                        },
                        success: (data) => {
                            // Обновляем список специальностей
                            $('#specialtyFilter').html(data);
                        }
                    });
                }

                // Вызываем функцию при загрузке страницы
                updateSpecialties();

                // Обработчик изменения значения фильтра
                $('#filter_specialty').on('keyup', updateSpecialties);
                
            });
        </script>
<script type="text/javascript">

            function getUrlParameters() {
    let url = window.location.href;
    let urlParts = url.split('?');
    let parameters = {};

    if (urlParts.length > 1) {
        let queryString = urlParts[1];
        let queryParams = queryString.split('&');

        for (let i = 0; i < queryParams.length; i++) {
            let param = queryParams[i].split('=');
            let paramName = decodeURIComponent(param[0]);
            let paramValue = decodeURIComponent(param[1]);

            // Если параметр уже существует в объекте parameters,
            // добавляем его значение в массив значений
            if (parameters.hasOwnProperty(paramName)) {
                if (Array.isArray(parameters[paramName])) {
                    parameters[paramName].push(paramValue);
                } else {
                    parameters[paramName] = [parameters[paramName], paramValue];
                }
            } else {
                parameters[paramName] = paramValue;
            }
        }
    }

    return parameters;
}




           function goFilter(event) {
                event.preventDefault();
                let specialtyFilter = document.getElementById('specialtyFilter').value;
                let nameFilter = document.getElementById('nameFilter').value;
                let urlParams = getUrlParameters();
                let form = document.getElementById('filter');
                let selectElement = document.getElementById('specialtyFilter');
                console.log(urlParams);
                if (Object.keys(urlParams).length === 0 && urlParams.constructor === Object) {
                    form.submit();
                }
                let selectedSpecialties = urlParams['specialty[]'];
                if (nameFilter === "" &&  typeof nameFilter !== 'undefined' &&  urlParams['name'] !== "") {
                    document.getElementById('nameFilter').value = urlParams['name'];
                }
                
                if (Array.isArray(selectedSpecialties) && selectedSpecialties.length > 0) {
                    selectedSpecialties.forEach(function(specialty, index) {
                        // Удаляем все специальные символы из строки
                        let sanitizedSpecialty = specialty.replace(/\+/g, " ");
                        // Заменяем текущий элемент массива очищенным значением
                        selectedSpecialties[index] = sanitizedSpecialty;
                    });
                    // Продолжаем с выбором соответствующих опций в selectElement
                    for (let i = 0; i < selectElement.options.length; i++) {
                        // Проверяем, если значение опции входит в массив выбранных специальностей
                        // console.log(selectElement.options[i].value)
                        if (selectedSpecialties.indexOf(selectElement.options[i].value) !== -1) {
                            // Делаем опцию выбранной
                            selectElement.options[i].selected = true;
                        }
                    }
                } else {
                    // console.log("selectedSpecialties is empty or not an array");
                }
                console.log(document.getElementById('specialtyFilter').value)
                console.log(document.getElementById('nameFilter').value)
                form.submit();
            }
        </script>


        
        <?php 

        if (!isset($_SESSION["login"])) {
            if (isset($_GET['registration'])) {
                echo '<script>registration();</script>';
            } else {
                echo '<script>authorization();</script>';
            }
        }


        ?>
        <?php $conn->close(); ?>
    </body>
</html>
