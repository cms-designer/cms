<?php
class ContentController {

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

    // CSRF-Token setzen (private Hilfsfunktion)
    private function ensureCsrfToken() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function index() {
        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? 1; // Standard: normaler Benutzer

        $model = new Content();

        if ($role == 0) {
            // Admin: alle Inhalte laden
            $contents = $model->all();
        } else {
            // Nur Inhalte der eigenen Gruppen laden
            $contents = $model->getByUserGroups($userId);
        }

        // CSRF-Token für Löschformulare setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/content_index.php';
    }

    public function create() {
        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        // Nur Admins (role == 0) dürfen Content anlegen!
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Content&a=index');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken();
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            if ($title === '' || $content === '') {
                $error = "Bitte alle Felder ausfüllen.";
            } else {
                $contentModel = new Content();
                $contentModel->create($title, $content);
                header('Location: index.php?c=Content&a=index');
                exit;
            }
        }
        // CSRF-Token für das Formular setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/content_form.php';
    }

    public function edit() {
        Session::start();
        $id = $_GET['id'] ?? null;
        $contentModel = new Content();
        $entry = $contentModel->find($id);
        $error = '';
        if (!$entry) {
            $error = "Eintrag nicht gefunden.";
            include __DIR__ . '/../views/content_form.php';
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken();
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            if ($title === '' || $content === '') {
                $error = "Bitte alle Felder ausfüllen.";
            } else {
                $contentModel->update($id, $title, $content);
                header('Location: index.php?c=Content&a=index');
                exit;
            }
        }
        // CSRF-Token für das Formular setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/content_form.php';
    }

    public function delete() {
        Session::start();
        // Nur POST zulassen!
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=Content&a=index');
            exit;
        }
        $this->checkCsrfToken();
        $id = $_GET['id'] ?? null;
        $contentModel = new Content();
        $contentModel->delete($id);
        header('Location: index.php?c=Content&a=index');
        exit;
    }

    public function assignGroups() {
        Session::start();
        $contentId = $_GET['id'] ?? null;
        if (!$contentId) {
            header("Location: index.php?c=Content&a=index");
            exit;
        }

        $contentModel = new Content();
        $entry = $contentModel->find($contentId);

        // Alle Gruppen holen
        $groupModel = new Group();
        $allGroups = $groupModel->all();

        // Zugewiesene Gruppen IDs holen
        $assignedGroups = array_column($contentModel->getGroups($contentId), 'id');

        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken();
            $selectedGroups = $_POST['groups'] ?? [];
            $contentModel->setGroups($contentId, $selectedGroups);
            $success = "Gruppen wurden gespeichert!";
            // Nach dem Speichern die aktuellen Gruppen laden
            $assignedGroups = $selectedGroups;
        }
        // CSRF-Token für das Formular setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/content_assign_groups.php';
    }
}