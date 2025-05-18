<?php
class Language
{
    private $languages = [
        1 => 'Deutsch',
        2 => 'English'
        // Weitere Sprachen können hier ergänzt werden
    ];

    /**
     * Gibt alle verfügbaren Sprachen als Array zurück.
     */
    public function getAll()
    {
        return $this->languages;
    }

    /**
     * Holt die aktuell eingestellte Sprache aus der Datenbank.
     * @return int Sprach-ID (z.B. 1 für Deutsch, 2 für Englisch)
     */
    public function getCurrent()
    {
        $config = require __DIR__ . '/../config/config.php';
        $db = $config['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $pdo = new PDO($dsn, $db['user'], $db['pass']);

        $stmt = $pdo->query("SELECT language FROM basis LIMIT 1");
        $langId = $stmt->fetchColumn();
        if ($langId === false) {
            return 1; // Standard: Deutsch
        }
        return (int)$langId;
    }

    /**
     * Setzt die Sprache (global) in der Datenbank.
     */
    public function setCurrent($langId)
    {
        $config = require __DIR__ . '/../config/config.php';
        $db = $config['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $pdo = new PDO($dsn, $db['user'], $db['pass']);

        $stmt = $pdo->prepare("UPDATE basis SET language = ? LIMIT 1");
        $stmt->execute([(int)$langId]);
    }
}