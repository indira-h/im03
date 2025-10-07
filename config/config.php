<?php
// Datenbankverbindung für Infomaniak
$host = 'sv3l19.myd.infomaniak.com'; // <- Dein MySQL-Hostname (steht oben in phpMyAdmin)
$db   = 'sv3l19_virus_tracker';      // <- Name deiner Datenbank
$user = 'sv3l19_virus';      // <- Dein Datenbankbenutzer (meist gleich wie DB-Name)
$pass = 'infomAniak6!';       // <- Das Passwort, das du bei Infomaniak für den DB-User gesetzt hast
$charset = 'utf8mb4';

// PDO Verbindung aufbauen
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Verbindung erfolgreich!"; // zum Testen aktivieren
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed', 'details' => $e->getMessage()]);
    exit;
}
?>
