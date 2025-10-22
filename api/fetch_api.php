<?php
include("../config/config.php");

// Holt Daten aus API
$apiUrl = "https://im03.ch/api/data.php?range=30";

// Daten herunterladen (cURL)
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false
]);
$json = curl_exec($ch);
if (curl_errno($ch)) {
    die("API-Fehler: " . curl_error($ch));
}
curl_close($ch);

// JSON umwandeln
$data = json_decode($json, true);
if (!is_array($data)) {
    die("Keine gültigen Daten erhalten!");
}

// In Datenbank speichern
foreach ($data as $row) {
    $datum = $row["datum"];
    $wert = $row["viruswert"];
    $risiko = $row["risiko_level"];

    $stmt = $pdo->prepare("INSERT INTO virus_data (datum, viruswert, risiko_level) VALUES (?, ?, ?)");
    $stmt->execute([$datum, $wert, $risiko]);
}

echo "Import abgeschlossen (lokale API).";
?>
