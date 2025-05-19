<?php
class SettingsController
{
    // CSRF-Token prüfen
    private function checkCsrfToken() {
        if (
            empty($_POST['csrf_token']) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            http_response_code(403);
            die('Ungültiges CSRF-Token!');
        }
    }

    // ... andere Methoden ...

    public function downloadDatabase()
    {
        Session::start();
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }
        // CSRF-Schutz!
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }
        $this->checkCsrfToken();

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
        Session::start();
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }
        // CSRF-Schutz!
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }
        $this->checkCsrfToken();

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
        // CSRF-Schutz!
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }
        $this->checkCsrfToken();

        require_once __DIR__ . '/../models/Database.php';
        $dbModel = new Database();
        $sqlData = $dbModel->exportAllAsSQLInsertsOnly();

        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="datenbank_export_inserts.sql"');
        echo $sqlData;
        exit;
    }
}