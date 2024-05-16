<?php session_start(); 

include "config.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки</title>
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
        </header>

    <div class="container mt-5">

        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Публикации</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Направления</a>
         
          </div>
        </nav>
      
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">.

            <form action="" method="POST">
            <div class="form-group">
                <label for="start_year">Год начала публикаций:</label>
                <input type="text" class="form-control" id="start_year" name="start_year" required value="<?php print_r($conn->query("SELECT `start_year` FROM `setting`")->fetch_assoc()['start_year']); ?>">
            </div>
            <div class="form-group">
                <label for="end_year">Год окончания публикаций:</label>
                <input type="text" class="form-control" id="end_year" name="end_year" required value="<?php print_r($conn->query("SELECT `end_year` FROM `setting`")->fetch_assoc()['end_year']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Применить настройки</button>
        </form>
          </div>
          <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
<form action="updateDirectionsOnMysql.php" method="POST" enctype="multipart/form-data">
    <p>Для обновления списка Вам нужно проделать следующие пункты</p>
    <ol>
        <li><a href="getDirectionsOnFile.php">Скачать текущий список (нажмите что бы скачать excel докмент)</a></li>
        <li>Провести любые изменения. Можно как удалить строку в документе, так и добавить/изменить значение/строку</li>
        <li>Добавить изменённый файл: <input type="file" name="uploaded_file"></li>
        <li>Нажать кнопку: <button class="btn btn-primary">Обновить список</button></li>
    </ol>
    </form>
    <h2>Текущий список:</h2>
    <?php

// Обработка формы регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_year = $_POST["start_year"];
    $end_year = $_POST["end_year"];

    $sql = "UPDATE `setting` SET `start_year`= $start_year, `end_year`= $end_year WHERE `id` = 1";

    if ($conn->query($sql) === TRUE) {
        echo "Год изменён";

    } else {
        // echo "Ошибка: " . $sql . "<br>" . $conn->error;
        echo "";
    }
    header("Refresh: 0");
}


?>

        <?php
$sql = "SELECT `Direction` AS 'Направление подготовки', `OldCipher` AS 'Научные специальности (старый шифр)', `NewCipher` AS 'Научные специальности (новый шифр)' FROM `specialties` ORDER BY `NewCipher` ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Получаем все данные из результата запроса в виде массива
    $data = $result->fetch_all(MYSQLI_ASSOC);

    // Вывод таблицы HTML
    echo '<table class="table table-bordered my-3">
        <tr>
            <th>Направление подготовки</th>
            <th>Научные специальности (старый шифр)</th>
            <th>Научные специальности (новый шифр)</th>
        </tr>';
    foreach ($data as $row) {
        echo '<tr>';
        echo '<td>' . $row['Направление подготовки'] . '</td>';
        echo '<td>' . $row['Научные специальности (старый шифр)'] . '</td>';
        echo '<td>' . $row['Научные специальности (новый шифр)'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<tr><td>Список специальностей пуст</td></tr>';
}

// Закрываем соединение
$conn->close();
?>

          </div>
        </div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>


