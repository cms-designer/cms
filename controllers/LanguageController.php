<?php
class LanguageController
{
    // CSRF-Token prüfen (private Hilfsfunktion)
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

    // CSRF-Token erzeugen (private Hilfsfunktion)
    private function ensureCsrfToken() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function index()
    {
        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }

        require_once __DIR__ . '/../models/Language.php';
        $languageModel = new Language();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
            $this->checkCsrfToken();
            $languageModel->setCurrent($_POST['language']);
            header('Location: index.php?c=Language&a=index');
            exit;
        }

        // CSRF-Token für das Formular setzen
        $this->ensureCsrfToken();

        $languages = $languageModel->getAll();
        $currentLanguage = $languageModel->getCurrent();

        include __DIR__ . '/../views/settings.php';
    }
}