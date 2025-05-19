<?php
class AuthController {
    public function login() {
        // Session starten, falls noch nicht gestartet
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // CSRF-Token generieren, wenn nicht vorhanden (IMMER, auch bei GET!)
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // CSRF-Token prüfen (mit empty() für besseren Schutz!)
            if (
                empty($_POST['csrf_token']) ||
                empty($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
            ) {
                http_response_code(403);
                $error = "Ungültiges CSRF-Token!";
                // Token für erneuten Versuch neu setzen (optional, aber sicherer)
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                include __DIR__ . '/../views/login.php';
                exit;
            }

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $userModel = new User();
            $user = $userModel->findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                // Session-Fixation verhindern: neue Session-ID generieren
                session_regenerate_id(true);

                // Benutzerdaten für Header & Auth in die Session schreiben
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: index.php?a=adminpanel');
                exit;
            } else {
                $error = "Login fehlgeschlagen!";
            }
        }
        // Beim ersten GET oder Fehler wird das Token angezeigt
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