<?php
include("../config/config.php");

$apiUrl = "https://api.opendisease.sh/v3/covid-19/historical/switzerland?lastdays=30";
$json = file_get_contents($apiUrl);
$data = json_decode($json, true);

foreach ($data['timeline']['cases'] as $date => $value) {
    $stmt = $pdo->prepare("INSERT INTO virus_data (datum, viruswert, risiko_level) VALUES (?, ?, ?)");
    $risiko = $value > 20000 ? 'hoch' : ($value > 10000 ? 'mittel' : 'niedrig');
    $stmt->execute([$date, $value, $risiko]);
}

echo "Import abgeschlossen.";
?>
