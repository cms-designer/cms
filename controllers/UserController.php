<?php
class UserController {
    public function index() {
		/* nur eingeloggte Benutzer
		        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        } 
		...aber es sollen ja nur Admins und nicht normale Benutzer Benutzer index sehen also: */
		
		    Session::start();
    // Nur für eingeloggte Admins sichtbar
    if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
        $model = new User();
        $users = $model->getAll();
        include __DIR__ . '/../views/users.php';
    }
public function create() {
    Session::start();
    // Nur für eingeloggte Admins sichtbar
    if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $model = new User();
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $role = isset($_POST['role']) ? (int)$_POST['role'] : 1;

        // Prüfe, ob Benutzername existiert:
        if ($model->findByUsername($username)) {
            $error = 'Benutzername existiert bereits!';
        } else {
            $model->create($username, $password, $email, $role);
            header('Location: index.php?c=User&a=index');
            exit;
        }
    }
    include __DIR__ . '/../views/user_form.php';
}

public function edit() {
    Session::start();
    if (!Session::isLoggedIn()) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
    $model = new User();
    $user = $model->findByUsername($_GET['username']);
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = isset($_POST['role']) ? (int)$_POST['role'] : 1;
        $model->update($user['id'], $username, $email, $role);
        header('Location: index.php?c=User&a=index');
        exit;
    }
    include __DIR__ . '/../views/user_form.php';
}

public function changePassword() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $userId = $_GET['id'] ?? null;
    $error = '';
    $success = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        if ($password && $password === $password_confirm) {
            $userModel = new User();
            $userModel->changePassword($userId, $password);
            $success = 'Passwort geändert!';
        } else {
            $error = 'Passwörter stimmen nicht überein!';
        }
    }
    include __DIR__ . '/../views/user_change_password.php';
}


public function delete() {
    Session::start();
    // Nur Admins dürfen Benutzer löschen!
    if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
        header('Location: index.php?c=User&a=index');
        exit;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        header('Location: index.php?c=User&a=index');
        exit;
    }

    $userModel = new User();
    $user = $userModel->findById($id);

    // Admins können sich nicht selbst löschen (optional)
    if ($user && $user['id'] == $_SESSION['user_id']) {
        // Optional: Fehlermeldung setzen oder anzeigen
        header('Location: index.php?c=User&a=index');
        exit;
    }

    $userModel->delete($id);
    header('Location: index.php?c=User&a=index');
    exit;
}

public function assignGroups() {
    $userId = $_GET['id'] ?? null;
    if (!$userId) {
        header("Location: index.php?c=User&a=index");
        exit;
    }

    $userModel = new User();
    $user = $userModel->find($userId);
    if (!$user) {
        echo "Benutzer nicht gefunden!";
        exit;
    }

    $groupModel = new Group();
    $allGroups = $groupModel->all();
    $assignedGroups = array_column($groupModel->getGroupsByUser($userId), 'id');

    $success = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedGroups = $_POST['groups'] ?? [];
        $groupModel->setGroupsForUser($userId, $selectedGroups);
        $success = "Gruppen wurden gespeichert!";
        $assignedGroups = $selectedGroups;
    }

    include __DIR__ . '/../views/user_assign_groups.php';
}
}