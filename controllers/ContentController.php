<?php
class ContentController {
	
	/*
    public function index() {
		    Session::start();
    if (!Session::isLoggedIn()) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
        $contentModel = new Content();
        $contents = $contentModel->all();
        include __DIR__ . '/../views/content_index.php';
    }
*/

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
        // Optional: Fehlermeldung oder Weiterleitung
        header('Location: index.php?c=Content&a=index');
        exit;
    }

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    include __DIR__ . '/../views/content_form.php';
}

    public function edit() {
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
        include __DIR__ . '/../views/content_form.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        $contentModel = new Content();
        $contentModel->delete($id);
        header('Location: index.php?c=Content&a=index');
        exit;
    }
	
	
public function assignGroups() {
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
        $selectedGroups = $_POST['groups'] ?? [];
        $contentModel->setGroups($contentId, $selectedGroups);
        $success = "Gruppen wurden gespeichert!";
        // Nach dem Speichern die aktuellen Gruppen laden
        $assignedGroups = $selectedGroups;
    }

    include __DIR__ . '/../views/content_assign_groups.php';
}


}