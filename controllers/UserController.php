<?php
class UserController {
    // Hilfsfunktion: CSRF-Token sicherstellen
    private function ensureCsrfToken() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Hilfsfunktion: CSRF-Token prüfen
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

    public function index() {
        Session::start();
        // Nur für eingeloggte Admins sichtbar
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        $this->ensureCsrfToken();
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
        $this->ensureCsrfToken();
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->checkCsrfToken();
            $model = new User();
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = isset($_POST['role']) ? (int)$_POST['role'] : 1;

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
        $this->ensureCsrfToken();
        $model = new User();
        $user = $model->findByUsername($_GET['username'] ?? '');
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->checkCsrfToken();
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = isset($_POST['role']) ? (int)$_POST['role'] : 1;
            $model->update($user['id'], $username, $email, $role);
            header('Location: index.php?c=User&a=index');
            exit;
        }
        include __DIR__ . '/../views/user_form.php';
    }

    public function changePassword() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $this->ensureCsrfToken();
        $userId = $_GET['id'] ?? null;
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken();
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
        // CSRF-Schutz und nur POST erlauben!
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=User&a=index');
            exit;
        }
        $this->checkCsrfToken();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?c=User&a=index');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($id);

        // Admins können sich nicht selbst löschen (optional)
        if ($user && $user['id'] == $_SESSION['user_id']) {
            header('Location: index.php?c=User&a=index');
            exit;
        }

        $userModel->delete($id);
        header('Location: index.php?c=User&a=index');
        exit;
    }

    public function assignGroups() {
        Session::start();
        // Nur für eingeloggte Admins sichtbar
        if (!Session::isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        $this->ensureCsrfToken();

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
            $this->checkCsrfToken();
            $selectedGroups = $_POST['groups'] ?? [];
            $groupModel->setGroupsForUser($userId, $selectedGroups);
            $success = "Gruppen wurden gespeichert!";
            $assignedGroups = $selectedGroups;
        }

        include __DIR__ . '/../views/user_assign_groups.php';
    }
}