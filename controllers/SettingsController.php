<?php
class SettingsController
{
    // ... andere Methoden ...

    public function downloadDatabase()
    {
        // Nur für eingeloggte Admins, falls nötig
        Session::start();
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }

        require_once __DIR__ . '/../models/Database.php';
        $dbModel = new Database();
        $csvData = $dbModel->exportAllAsCSV();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="datenbank_export.csv"');
        echo $csvData;
        exit;
    }

    public function downloadDatabaseSQL()
    {
        // Nur für eingeloggte Admins, falls nötig
        Session::start();
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }

        require_once __DIR__ . '/../models/Database.php';
        $dbModel = new Database();
        $sqlData = $dbModel->exportAllAsSQL();

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="datenbank_export.sql"');
        echo $sqlData;
        exit;
    }
	
	public function downloadDatabaseSQLInsertsOnly()
{
    Session::start();
    if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
        header('Location: index.php?c=Dashboard&a=index');
        exit;
    }

    require_once __DIR__ . '/../models/Database.php';
    $dbModel = new Database();
    $sqlData = $dbModel->exportAllAsSQLInsertsOnly();

    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="datenbank_export_inserts.sql"');
    echo $sqlData;
    exit;
}
}