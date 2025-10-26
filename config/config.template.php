<?php
// Diese Datei dient als VORLAGE für die echte config.php, hier sind extra keine Passwörter eingetragen.


// Verbindungseinstellungen zur Datenbank
$host = 'your-hostname';
$db   = 'your-database-name';
$user = 'your-database-user';
$pass = 'your-password';
$charset = 'utf8mb4';

// Verbindet alle oben definierten Infos in einer Zeichenkette (die PDO versteht)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";


//  Zusätzliche Einstellungen für PDO (Datenbankverbindung)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

//  Verbindung zur Datenbank aufbauen
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

 // Gibt eine JSON-Fehlermeldung mit Details aus
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}
?>