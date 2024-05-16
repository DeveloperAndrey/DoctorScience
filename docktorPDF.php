<?php session_start(); 
include "config.php";
require 'vendor/autoload.php'; // Путь к библиотеке PHPWord

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Language;

// Создаем новый объект PHPWord
$phpWord = new PhpWord();

$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(14);

// Установка русского языка правописания
$phpWord->getSettings()->setThemeFontLang(new Language(Language::RU_RU));

// Добавляем раздел в документ
$section = $phpWord->addSection();
$section->setPageMargin(1700, 1200, 1500, 1500); // левое, правое, верхнее, нижнее поля

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
$row = $result->fetch_assoc();
$applicant = ($row["Приемлемость аспиранта Руч."] != NULL) ? $row["Приемлемость аспиранта Руч."] : $row["Претенденты ФИО"];
$textRun = $section->addTextRun(array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
$textRun->addText($applicant, array('size' => 16, 'bold' => true));
$textRun->addTextBreak();
$result = $conn->query($sql);
            if ($result->num_rows > 0) {
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

                    $a_VAK = ($vak != NULL) ? '<a style="color:blue;" href="'.urlencode($vak).'" target="_blank">Ссылка на ВАК</a>' : "";
                    $a_Elibrary = ($elibrary != NULL) ? '<a style="color:blue;" href="'.urlencode($elibrary).'" target="_blank">Ссылка на eLIBRARY.RU</a>' : "";
                    $a_Dissernet = ($dissernet != NULL) ? '<a style="color:blue;" href="'.urlencode($dissernet).'" target="_blank">Ссылка на Диссернет</a>' : "";
                    $a_Abstract = ($abstract != NULL) ? '<a style="color:blue;" href="'.urlencode($abstract).'" target="_blank">Ссылка на Автореферат</a>' : "";
                    $id = $row["id"];
                     $button = '';
                     $disabled = 'disabled';
                    if(isset($_SESSION['admin'])){
                        if($_SESSION['admin']){
                           $button = ' </br><button type="submit" class="btn btn-primary my-2">Обновить данные доктора</button>';
                           $disabled = '';
                        }
                    }

                }
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'cellWidth' => '50%', 'width' => '1400px']);
            $table->setWidth('1400px');

            $linkStyle = array(
                'color' => 'blue',
                'textDecoration' => 'underline'
            );
            $table->addRow();
            $table->addCell(2000)->addText('ВАК', ['bold' => true]);
            if(!empty($vak)){
                $cell = $table->addCell(2000);
                $cell->addLink($vak, 'Ссылка на ВАК', $linkStyle);
            }
            else $table->addCell(2000)->addText('-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Elibrary', ['bold' => true]);
            if(!empty($elibrary)){
            $cell = $table->addCell(2000);
            $cell->addLink($elibrary, 'Ссылка на Elibrary', $linkStyle);
            }
            else $table->addCell(2000)->addText('-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Dissernet', ['bold' => true]);
            if(!empty($dissernet)){
                $cell = $table->addCell(2000);
                $cell->addLink($dissernet, 'Ссылка на Dissernet', $linkStyle);
            }
            else $table->addCell(2000)->addText('-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Автореферат', ['bold' => true]);
            if(!empty($abstract)){
                $cell = $table->addCell(2000);
                $cell->addLink($abstract, 'Ссылка на Автореферат', $linkStyle);
            }
            else $table->addCell(2000)->addText('-');

            $table->addRow();
            $table->addCell(2000)->addText('Шифр научной специальности, по которой защищена диссертация', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($cipher)&&$cipher!='Записи нет'&&$cipher!='Нет данных' ? $cipher : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Ученое звание', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($rank)&&$rank!='Записи нет'&&$rank!='Нет данных' ? $rank : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Год защиты диссертации', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($year)&&$year!='Записи нет'&&$year!='Нет данных' ? $year : '-');

            $table->addRow();
            $table->addCell(2000)->addText('Место работы', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($work)&&$work!='Записи нет'&&$work!='Нет данных' ? $work : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Тема диссертации', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($subject)&&$subject!='Записи нет'&&$subject!='Нет данных' ? strip_tags($subject) : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Место защиты', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($protection)&&$protection!='Записи нет'&&$protection!='Нет данных' ? $protection : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Отрасль науки', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($industry)&&$industry!='Записи нет'&&$industry!='Нет данных' ? $industry : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Приказ', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($order)&&$order!='Записи нет'&&$order!='Нет данных' ? $order : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Решение', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($solution)&&$solution!='Записи нет'&&$solution!='Нет данных' ? $solution : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Количество публикаций на Elibrary', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($publications_count)&&$publications_count!='Записи нет'&&$publications_count!='Нет данных' ? $publications_count : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Author ID', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($autorID)&&$autorID!='Записи нет'&&$autorID!='Нет данных' ? $autorID : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('SPIN-код', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($SPIN)&&$SPIN!='Записи нет'&&$SPIN!='Нет данных' ? $SPIN : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('WoS, Scopus (не менее 2)', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($wos_count)&&$wos_count!='Записи нет'&&$wos_count!='Нет данных' ? $wos_count : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('ВАК (не менее 5)', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($vak_count)&&$vak_count!='Записи нет'&&$vak_count!='Нет данных' ? $vak_count : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Издания категорий К-1 и К-2, RSCI, Q1 и Q2. (не менее 9)', ['bold' => true]);

            // Указываем rowspan для первой ячейки, чтобы объединить ее с тремя следующими строками
            $cell = $table->addCell(2000);
            $cell->addText('RSCI - '.$rsci_count);
            $cell->addText('K1 - '.$K1_count);
            $cell->addText('K2 - '.$K2_count);
            $cell->addText('K3 - '.$K3_count);
            
            $table->addRow();
            $table->addCell(2000)->addText('Диссертации претендента', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($a_my)&&$a_my!='Записи нет'&&$a_my!='Нет данных' ? $a_my : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Претендент является оппонентом/руководителем/консультантом', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($a_alien)&&$a_alien!='Записи нет'&&$a_alien!='Нет данных' ? $a_alien : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Публикации претендента', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($a_publication)&&$a_publication!='Записи нет'&&$a_publication!='Нет данных' ? $a_publication : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Номер телефона', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($phone)&&$phone!='Записи нет'&&$phone!='Нет данных' ? $phone : '-');
            
            $table->addRow();
            $table->addCell(2000)->addText('Адрес электронной почты', ['bold' => true]);
            $table->addCell(2000)->addText(!empty($mail)&&$mail!='Записи нет'&&$mail!='Нет данных' ? $mail : '-');
            }

            


if ($id_autor != NULL)  {
             $section = $phpWord->addSection();       
            $sql = "SELECT * FROM vak LEFT JOIN vakcategory2023 ON vak.journal = vakcategory2023.Name WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
            $result = $conn->query($sql);

            
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%']);

            // Заголовок таблицы
            $table->addRow();
            $cell = $table->addCell(5000, ['valign' => 'center']);
            $cell->addText('VAK', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
            $cell->getStyle()->setGridSpan(6);

            // Заголовки столбцов
            $table->addRow();
            $table->addCell(2000)->addText('Публикация', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Авторы', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Журнал', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Год', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Прочее', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Категория', ['bold' => true], ['alignment' => 'center']);
            // Данные из базы данных
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Добавление строки с данными в таблицу
                    $table->addRow();
                    $name = preg_replace_callback('/(?:^|\.\s+)\p{Ll}/u', function($matches) {
                        return mb_convert_case($matches[0], MB_CASE_TITLE, 'UTF-8');
                    }, mb_strtolower($row["name"], 'UTF-8'));
                    $linkStyle = array(
                        'color' => 'blue',
                        'textDecoration' => 'underline'
                    );
                    $cell = $table->addCell(2000);
                    $cell->addLink($row["link"], $name, $linkStyle);
                    $table->addCell(2000)->addText($row["autor"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["journal"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["year"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["more"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["Category"], null, ['alignment' => '']);
                }
            } else {
                // Если нет результатов
                $table->addRow();
                $cell = $table->addCell(10000, ['valign' => 'center']);
                $cell->addText('Нет результатов', ['alignment' => 'center']);
                $cell->getStyle()->setGridSpan(6);
            }

            $section = $phpWord->addSection();   
            $sql = "SELECT * FROM `wos` WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
            $result = $conn->query($sql);
			$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%']);

            // Заголовок таблицы
            $table->addRow();
            $cell = $table->addCell(5000, ['valign' => 'center']);
            $cell->addText('WOS', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
            $cell->getStyle()->setGridSpan(5);

            // Заголовки столбцов
            $table->addRow();
            $table->addCell(2000)->addText('Публикация', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Авторы', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Журнал', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Год', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Прочее', ['bold' => true], ['alignment' => 'center']);

            // Данные из базы данных
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Добавление строки с данными в таблицу
                    $table->addRow();
                    $name = preg_replace_callback('/(?:^|\.\s+)\p{Ll}/u', function($matches) {
                        return mb_convert_case($matches[0], MB_CASE_TITLE, 'UTF-8');
                    }, mb_strtolower($row["name"], 'UTF-8'));
                    $linkStyle = array(
                        'color' => 'blue',
                        'textDecoration' => 'underline'
                    );
                    $cell = $table->addCell(2000);
                    $cell->addLink($row["link"], $name, $linkStyle);
                    $table->addCell(2000)->addText($row["autor"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["journal"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["year"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["more"], null, ['alignment' => '']);
                }
            } else {
                // Если нет результатов
                $table->addRow();
                $cell = $table->addCell(10000, ['valign' => 'center']);
                $cell->addText('Нет результатов', ['alignment' => 'center']);
                $cell->getStyle()->setGridSpan(5);
            }
             $section = $phpWord->addSection();   
            $sql = "SELECT * FROM `rsci` WHERE `link_autor` = '$id_autor' ORDER BY year DESC";
            $result = $conn->query($sql);

			$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%']);

            // Заголовок таблицы
            $table->addRow();
            $cell = $table->addCell(5000, ['valign' => 'center']);
            $cell->addText('RSCI', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
            $cell->getStyle()->setGridSpan(5);

            // Заголовки столбцов
            $table->addRow();
            $table->addCell(2000)->addText('Публикация', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Авторы', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Журнал', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Год', ['bold' => true], ['alignment' => 'center']);
            $table->addCell(2000)->addText('Прочее', ['bold' => true], ['alignment' => 'center']);

            // Данные из базы данных
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Добавление строки с данными в таблицу
                    $table->addRow();
                    $name = preg_replace_callback('/(?:^|\.\s+)\p{Ll}/u', function($matches) {
                        return mb_convert_case($matches[0], MB_CASE_TITLE, 'UTF-8');
                    }, mb_strtolower($row["name"], 'UTF-8'));
                    $linkStyle = array(
                        'color' => 'blue',
                        'textDecoration' => 'underline'
                    );
                    $cell = $table->addCell(2000);
                    $cell->addLink($row["link"], $name, $linkStyle);
                    $table->addCell(2000)->addText($row["autor"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["journal"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["year"], null, ['alignment' => '']);
                    $table->addCell(2000)->addText($row["more"], null, ['alignment' => '']);
                }
            } else {
                // Если нет результатов
                $table->addRow();
                $cell = $table->addCell(10000, ['valign' => 'center']);
                $cell->addText('Нет результатов', ['alignment' => 'center']);
                $cell->getStyle()->setGridSpan(5);
            }


            } 
            // Закрываем соединение
            $conn->close();




// Сохраняем документ в файл
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$filename = ''.$applicant.'.docx';
$objWriter->save('file/'.$filename);

// Отправка заголовков для загрузки файла
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize('file/'.$filename));

// Отправка содержимого файла
readfile('file/'.$filename);

// После отправки файла, можно перенаправить пользователя на другую страницу
header("Location: docktor.php?id=$id");
exit();
?>