<?php
function loadLanguage() {
    // Konfigurationsdaten einbinden
    $config = require __DIR__ . '/config.php';
    $db = $config['db'];

    // Datenbankverbindung herstellen
    $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
    $pdo = new PDO($dsn, $db['user'], $db['pass']);

    $stmt = $pdo->query("SELECT language FROM basis LIMIT 1");
    $langId = $stmt->fetchColumn();

    // Sprache ggf. aus Session überschreiben
    if (isset($_SESSION['language'])) {
        $langId = $_SESSION['language'];
    }

    switch ($langId) {
        case 2:
            require_once __DIR__ . '/lang/en.php';
            break;
        case 1:
        default:
            require_once __DIR__ . '/lang/de.php';
            break;
    }
}