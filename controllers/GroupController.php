<?php
class GroupController {

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
        // Nur für eingeloggte Admins sichtbar
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        $model = new Group();
        $groups = $model->all(); // Passe ggf. auf deinen Methodennamen an!
        // CSRF-Token für Löschformulare setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/groups.php';
    }

    public function create() {
        Session::start();
        // Nur für eingeloggte Admins sichtbar
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->checkCsrfToken();
            $model = new Group();
            $model->create($_POST['name']);
            header('Location: index.php?c=Group&a=index');
            exit;
        }
        // CSRF-Token für das Formular setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/group_form.php';
    }

    public function edit() {
        Session::start();
        // Nur für eingeloggte Admins sichtbar
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        $model = new Group();

        // Hole die ID aus GET und lade die richtige Gruppe
        $groupId = $_GET['id'] ?? null;
        if (!$groupId) {
            header('Location: index.php?c=Group&a=index');
            exit;
        }
        $group = $model->find($groupId); // Passe ggf. an deinen Methodennamen an!
        if (!$group) {
            echo "Gruppe nicht gefunden!";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->checkCsrfToken();
            $model->update($groupId, $_POST['name']);
            header('Location: index.php?c=Group&a=index');
            exit;
        }
        // CSRF-Token für das Formular setzen
        $this->ensureCsrfToken();
        include __DIR__ . '/../views/group_form.php';
    }

    public function delete() {
        Session::start();
        // Nur für eingeloggte Admins sichtbar
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        // Löschvorgänge immer als POST absichern!
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=Group&a=index');
            exit;
        }
        $this->checkCsrfToken();
        $model = new Group();
        // ID kann über GET oder POST kommen – hier POST wegen CSRF absicherung
        $groupId = $_GET['id'] ?? ($_POST['id'] ?? null);
        if ($groupId) {
            $model->delete($groupId);
        }
        header('Location: index.php?c=Group&a=index');
        exit;
    }
}