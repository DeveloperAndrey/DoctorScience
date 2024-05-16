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
        <style>
            .bg-dark-blue {background: #0a2b78;}
            th {    border-top: none !important;
                border-bottom: none !important;     min-width: 235px;}
            .sticky-top {top: -1px !important;}
            .sticky-left {
                position: sticky;
                left: 0;
                background-color: #f8f9fa; /* Цвет фона первого столбца */
                z-index: 1;
            }
            .sticky-left-specialty {
            position: sticky;
            left: 139px; /* Зависит от ширины первого столбца */
            background-color: #f8f9fa; /* Цвет фона второго столбца */
            z-index: 1;
            }
            .bg-red {background: #fdc6c6;}
            .bg-yellow {background: #f7f0b3;}
             #filters, #paginator {
            position: fixed;
            bottom: 0;
            background-color: #ffffff;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1021;
            }
            #paginator {left: 0;}
            #filters {right: 0;}
            .dropdown:hover .dropdown-menu {
                display: block;
            }
            header a {color: #fff;}
            header a:hover {color: #e4e4e4; text-decoration: none;}
        </style>
        <header>
            <div class="bg-dark-blue">
                <div class="container py-3 text-white d-flex justify-content-between align-items-center">
                    <h3><a href="index.php">ИС «Доктора»</a></h3>
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
                <form action="WH_VAK_Step1.php" class="my-2" method="POST">
                    <button type="submit" class="btn btn-primary">Запуск модуля агрегации ВАК (шаг 1)</button>
                </form>
                <form action="WH_VAK_Step2.php" class="my-2" method="POST">
                    <button type="submit" class="btn btn-primary">Запуск модуля агрегации ВАК (шаг 2)</button>
                </form>
                <form action="WH_Dissernet.php" class="my-2" method="POST">
                    <button type="submit" class="btn btn-primary">Запуск модуля агрегации Диссернет</button>
                </form>
            </div>
        </header>

                    

                    <!-- </br><a href="registration_doctors.php" target="_blank" class="btn btn-primary">Добавить Доктора на агригацию данных</a> -->
  
            <div class="m-3" >
            <h2>Таблица агрегации</h2>
            <div style="overflow-x: scroll;">
            <?php

            // Указываем номер страницы и количество записей на странице
            $pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $pageSize = 100;

            // Вычисляем смещение (OFFSET) на основе номера страницы и количества записей на странице
            $offset = ($pageNumber - 1) * $pageSize;

            // Выбираем данные с использованием LIMIT и OFFSET
            $sql = "SELECT * FROM vak_step1 GROUP BY `fio`, `dissertation`, `date` LIMIT $pageSize OFFSET $offset";
            $result = $conn->query($sql);


            // Вывод данных

            echo '<table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="sticky-left">Претендент ФИО</th>
                                <th scope="col" >Год защиты</th>
                                <th scope="col" >Тема диссертации</th>
                                <th scope="col" >Ссылка VAK</th>
                                <th scope="col" >Тип</th>
                                <th scope="col" >Дата изменения</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($result->num_rows > 0) {
                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {
                    // Обработка и вывод данных
                    echo '
                    <tr>
                        <td class="sticky-left">'.$row["fio"]. '</td>
                        <td>'. date("Y", strtotime($row["date"])).'</td>
                        <td>'.$row["dissertation"].'</td>
                        <td><a href="'.$row["href"].'" target="_blank">'.$row["href"].'</a></td>
                        <td>'.$row["type"].'</td>
                        <td>'.$row["data_update"].'</td>
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

            echo '</tbody>
                </table>';

            ?>
        </div>
        <?php
        // Подсчитываем общее количество записей
            $totalRecords = $conn->query("SELECT COUNT(*) as count FROM vak_step1 GROUP BY `fio`, `dissertation`, `date`")->num_rows;

            // Вычисляем общее количество страниц
            $totalPages = ceil($totalRecords / $pageSize);

            // Закрываем соединение
            $conn->close();

            // Вывод ссылок на предыдущую и следующую страницы
            echo '<div class="m-3">Страницы: ';
            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='module1.php?page=$i'>$i</a> ";
            }
            echo "<div>Итого претендентов: $totalRecords</div>";
            echo '</div>';
            
            ?>
     </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    </body>
</html>
