<?php
class GroupController {

    public function index() {
    Session::start();
    // Nur für eingeloggte Admins sichtbar
    if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
        $model = new Group();
        // ACHTUNG: Methode heißt meist all() statt getAll()
        $groups = $model->all(); // Passe ggf. auf deinen Methodennamen an!
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
            $model = new Group();
            $model->create($_POST['name']);
            header('Location: index.php?c=Group&a=index');
            exit;
        }

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
            $model->update($groupId, $_POST['name']);
            header('Location: index.php?c=Group&a=index');
            exit;
        }
        include __DIR__ . '/../views/group_form.php';
    }

    public function delete() {
    Session::start();
    // Nur für eingeloggte Admins sichtbar
    if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
        $model = new Group();
        $groupId = $_GET['id'] ?? null;
        if ($groupId) {
            $model->delete($groupId);
        }
        header('Location: index.php?c=Group&a=index');
    }
}