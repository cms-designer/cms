<?php
class Database
{
    private $pdo;

    public function __construct()
    {
        // Lade das Array aus der config
        $config = include __DIR__ . '/../config/config.php';
        $db = $config['db'];

        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $user = $db['user'];
        $pass = $db['pass'];

        $this->pdo = new PDO(
            $dsn,
            $user,
            $pass,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$db['charset']}'"]
        );
    }

    public function exportAllAsCSV()
    {
        // Alle Tabellennamen laden
        $tables = [];
        $stmt = $this->pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $csv = '';
        foreach ($tables as $table) {
            // Tabellenkopf
            $csv .= "Tabelle: $table\n";
            $stmt = $this->pdo->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($rows)) {
                $csv .= "Keine Daten vorhanden\n\n";
                continue;
            }

            // Spaltennamen
            $csv .= implode(';', array_keys($rows[0])) . "\n";
            // Datensätze
            foreach ($rows as $row) {
                $csv .= implode(';', array_map(function($v) {
                    return str_replace([';', "\n", "\r"], ['\\;', ' ', ' '], $v);
                }, $row)) . "\n";
            }
            $csv .= "\n";
        }
        return $csv;
    }

    public function exportAllAsSQL()
    {
        // Alle Tabellennamen laden
        $tables = [];
        $stmt = $this->pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- SQL-Export für CMS Designer\nSET NAMES utf8mb4;\n";
        foreach ($tables as $table) {
            // Struktur
            $stmt2 = $this->pdo->query("SHOW CREATE TABLE `$table`");
            $row2 = $stmt2->fetch(PDO::FETCH_NUM);
            $sql .= "\n--\n-- Struktur für Tabelle `$table`\n--\n\n";
            $sql .= $row2[1] . ";\n";

            // Daten
            $stmt3 = $this->pdo->query("SELECT * FROM `$table`");
            $rows = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $sql .= "\n--\n-- Daten für Tabelle `$table`\n--\n";
                foreach ($rows as $row3) {
                    $vals = array_map(function($v) {
                        return $v === null ? 'NULL' : $this->pdo->quote($v);
                    }, array_values($row3));
                    $sql .= "INSERT INTO `$table` (`" . implode('`,`', array_keys($row3)) . "`) VALUES (" . implode(',', $vals) . ");\n";
                }
            }
            $sql .= "\n";
        }
        return $sql;
    }
	
	public function exportAllAsSQLInsertsOnly()
{
    $tables = [];
    $stmt = $this->pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $sql = "-- SQL-Export (nur Daten) für CMS Designer\nSET NAMES utf8mb4;\n\n";
    foreach ($tables as $table) {
        // Daten (INSERTs)
        $stmt3 = $this->pdo->query("SELECT * FROM `$table`");
        $rows = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            $sql .= "-- Daten für Tabelle `$table`\n";
            foreach ($rows as $row3) {
                $vals = array_map(function($v) {
                    return $v === null ? 'NULL' : $this->pdo->quote($v);
                }, array_values($row3));
                $sql .= "INSERT INTO `$table` (`" . implode('`,`', array_keys($row3)) . "`) VALUES (" . implode(',', $vals) . ");\n";
            }
            $sql .= "\n";
        }
    }
    return $sql;
}
}