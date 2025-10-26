<?php
include("../config/config.php");

// Externe API-Quelle
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

//  JSON in Array umwandeln
$data = json_decode($json, true);
if (!is_array($data)) {
    die("Keine gültigen Daten erhalten!");
}

// In Datenbank speichern
$sql = "
    INSERT INTO virus_data (datum, viruswert, risiko_level)
    VALUES (:datum, :wert, :risiko)
    ON DUPLICATE KEY UPDATE 
        viruswert = VALUES(viruswert),
        risiko_level = VALUES(risiko_level)
";
$stmt = $pdo->prepare($sql);

$inserted = 0;
foreach ($data as $row) {
    if (!isset($row["datum"]) || !isset($row["viruswert"])) continue;

    $datum  = date("Y-m-d", strtotime($row["datum"])); 
    $wert   = (float)$row["viruswert"];
    $risiko = $row["risiko_level"] ?? null;

    $stmt->execute([
        ':datum'  => $datum,
        ':wert'   => $wert,
        ':risiko' => $risiko
    ]);
    $inserted++;
}

echo "Import abgeschlossen: $inserted Einträge aktualisiert oder hinzugefügt.";
?>
