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
        <div class="m-3">
            <?php echo "Пользователь: {$_SESSION['fio']}"; ?>
            </br><a href="destroy.php">Выйти</a>
            </br><a href="docktors.php">Назад</a>
        </br><a href="registration_doctors.php">Добавить Доктора на агригацию данных</a>
        </div>
        <style>
            .sticky-left {
                position: sticky;
                left: 0;
                background-color: #f8f9fa; /* Цвет фона первого столбца */
                z-index: 1;
            }
        </style>
        <div>
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

            echo '<table class="table table-bordered m-3">
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
