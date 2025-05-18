<?php
class AuthController {
    public function login() {
        // Session starten, falls noch nicht gestartet
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $userModel = new User();
            $user = $userModel->findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                // Benutzerdaten für Header & Auth in die Session schreiben
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
				$_SESSION['role'] = $user['role'];
                // Alternativ für spätere Erweiterungen (optional):
                // $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
                header('Location: index.php?a=adminpanel');
                exit;
            } else {
                $error = "Login fehlgeschlagen!";
            }
        }
        include __DIR__ . '/../views/login.php';
    }

    public function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }
}