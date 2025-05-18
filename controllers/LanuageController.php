<?php
class LanguageController
{
    public function index()
    {
        Session::start();
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Dashboard&a=index');
            exit;
        }

        require_once __DIR__ . '/../models/Language.php';
        $languageModel = new Language();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
            $languageModel->setCurrent($_POST['language']);
            header('Location: index.php?c=Language&a=index');
            exit;
        }

        $languages = $languageModel->getAll();
        $currentLanguage = $languageModel->getCurrent();
/*
        // Debug-Ausgabe hier einfügen:
        var_dump(__DIR__ . '/../views/settings.php');
        var_dump(file_exists(__DIR__ . '/../views/settings.php'));
        exit;
*/
        include __DIR__ . '/../views/settings.php';
    }
}