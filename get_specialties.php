<?php
session_start();
include "config.php";

if (!empty($_GET['filter_specialty'])) {
    $filter_specialty = $_GET['filter_specialty'];
    $sqlSpecialties = "SELECT CONCAT(NewCipher, '  ', OldCipher) AS name FROM specialties WHERE `OldCipher` LIKE '%$filter_specialty%' OR `NewCipher` LIKE '%$filter_specialty%'";
} else {
    $sqlSpecialties = "SELECT CONCAT(NewCipher, '  ', OldCipher) AS name FROM specialties";
}

$resultSpecialties = $conn->query($sqlSpecialties);

if ($resultSpecialties->num_rows > 0) {
    // $options = '<option value="">Все</option>';
    $options = '';
    $specialtiesArray = array(); // Array to store specialties
    while ($rowSpecialty = $resultSpecialties->fetch_assoc()) {
        $row = $rowSpecialty;
        
        $text = str_replace('–', '-', $row['name']);
        $parts = explode('  ', $text, 2);

        if (count($parts) == 2 && !empty($parts[0]) && !empty($parts[1])) {
            $part1 = $parts[0];
            $part2 = $parts[1];
            $codeName1 = explode(' - ', $part1);
            $codeName2 = explode(' - ', $part2);
            if (count($codeName1) == 2 && count($codeName2) == 2) {
                if ($codeName1[1] == $codeName2[1]) {
                    $result = "{$codeName1[0]} ({$codeName2[0]}) – {$codeName1[1]}";
                } else {
                    $result = "{$codeName1[0]} – {$codeName1[1]} ({$codeName2[0]} - {$codeName2[1]})";
                }
            }
        } elseif (!empty($parts[0])) {
            $codeName1 = explode(' - ', $parts[0]);
            $result = "{$codeName1[0]} – {$codeName1[1]}";
        
        } elseif (!empty($parts[1])) {
            $codeName1 = explode(' - ', $parts[1]);
            $result = "{$codeName1[0]} – {$codeName1[1]}";
        } 
        
        // Check if the specialty already exists in the array
        if (!in_array($result, $specialtiesArray)) {
            $specialtiesArray[] = $result; // Add specialty to array
            $options .= '<option value="' . $row['name'] . '">' . $result . '</option>';
        }
    }
    echo $options;
} else {
    echo '<option value="">Список специальностей пуст</option>';
}
?>
