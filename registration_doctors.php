<?php session_start(); 
include "config.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация докторов</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="m-3">
            <?php echo "Пользователь: {$_SESSION['fio']}"; ?>
            </br><a href="destroy.php">Выйти</a>
            </br><a href="index.php">На главную</a>
        </div>


            <div class="container mt-5">
        <form action="" method="POST">
            <div class="form-group">
                <label for="fio">ФИО*:</label>
                <input type="text" class="form-control" id="fio" name="fio" required>
            </div>
            <div class="form-group">
                <label for="vak">Ссылка VAK:</label>
                <input type="text" class="form-control" id="vak" name="vak">
            </div>
            <div class="form-group">
                <label for="elibrary">Ссылка Elibrary:</label>
                <input type="text" class="form-control" id="elibrary" name="elibrary">
            </div>
            <div class="form-group">
                <label for="dissernet">Ссылка Диссернет:</label>
                <input type="text" class="form-control" id="dissernet" name="dissernet">
            </div>
            <button type="submit" class="btn btn-primary">Добавить Доктора на агригацию данных</button>
        </form>
    </div>
<?php

$sql = "SELECT * FROM `doctors_tgu`";
$result = $conn->query($sql); 
echo '<table class="table table-bordered m-3">
                        <thead>
                            <tr>
                                <th scope="col" >Претенденты ФИО</th>
                                <th scope="col" >Место работы</th>
                                <th scope="col" >Шифр научной специальности</th>
                                <th scope="col" >Год защиты</th>
                                <th scope="col" >Публикации</th>
                                <th scope="col" >Тема диссертации</th>
                                <th scope="col" >Контакты</th>
                                <th scope="col" >Кафедра</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($result->num_rows > 0) {
                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {
                    // Обработка и вывод данных
                    echo '
                    <tr>
                        <td>'.$row["Претенденты ФИО"].'</td>
                        <td>'.$row["Место работы"].'</td>
                        <td>'.$row["Шифр научной специальности"].'</td>
                        <td>'.$row["Год защиты"].'</td>
                        <td>'.$row["Публикации"].'</td>
                        <td>'.$row["Тема диссертации"].'</td>
                        <td>'.$row["Контакты"].'</td>
                        <td>'.$row["Кафедра"].'</td>
                        
                    </tr>
                    ';
                }
            } else {
                echo'
                    <tr>
                        <td colspan="8">Нет результатов</td>
                        
                    </tr>
                    ';
            }

            echo '</tbody>
                </table>';
$sql = "SELECT * FROM `agrigation`";
$result = $conn->query($sql); 

// Вывод данных

            echo '<table class="table table-bordered m-3">
                        <thead>
                            <tr>
                                <th scope="col" >Претенденты ФИО</th>
                                <th scope="col" >Ссылка на ВАК</th>
                                <th scope="col" >Ссылка на Elibrary</th>
                                <th scope="col" >Ссылка на Dissernet</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($result->num_rows > 0) {
                // print_r($result->fetch_assoc());
                while ($row = $result->fetch_assoc()) {
                    // Обработка и вывод данных
                    echo '
                    <tr>
                        <td>'.$row["fio"].'</td>
                        <td>'.$row["vak"].'</td>
                        <td>'.$row["elibrary"].'</td>
                        <td>'.$row["dissernet"].'</td>
                        
                    </tr>
                    ';
                }
            } else {
                echo'
                    <tr>
                        <td colspan="4">Нет результатов</td>
                        
                    </tr>
                    ';
            }

            echo '</tbody>
                </table>';



?>


       

<div class="container">
    <?php

// Обработка формы регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fio = $_POST["fio"];
    $vak = $_POST["vak"];
    $elibrary = $_POST["elibrary"];
    $dissernet = $_POST["dissernet"];

    $sql = "INSERT INTO `agrigation`(`fio`, `vak`, `elibrary`, `dissernet`) VALUES ('$fio','$vak', '$elibrary','$dissernet')";

    if ($conn->query($sql) === TRUE) {
        echo "Доктор добавлен к очереди на агригацию данных";
    } else {
        // echo "Ошибка: " . $sql . "<br>" . $conn->error;
        echo "";
    }
}

$conn->close();
?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>


