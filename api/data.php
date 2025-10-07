<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// include("../config/config.php");  // ← aktivieren, wenn DB vorhanden

$range = isset($_GET['range']) ? intval($_GET['range']) : 30;

// Wenn noch keine Datenbank da ist → Mockdaten zurückgeben
if ($range === 7) {
    $data = [4, 6, 5, 11, 10, 18, 19];
} elseif ($range === 14) {
    $data = [9, 6, 5, 4, 4, 5, 6, 7, 12, 16, 14, 22, 28, 30];
} else {
    $data = [2,3,3,4,5,6,7,8,9,10,12,14,13,12,9,7,6,8,10,15,13,12,17,25,23,22,28,31,33,35];
}

// Wenn später DB aktiv ist:
// $stmt = $pdo->prepare("SELECT value FROM virus_data WHERE date >= DATE_SUB(NOW(), INTERVAL :days DAY)");
// $stmt->execute(['days' => $range]);
// $data = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($data);
?>
