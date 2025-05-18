<?php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            // Session-Fixation verhindern
            session_regenerate_id(true);
        }
    }
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
    }
    public static function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    public static function userId() {
        return $_SESSION['user_id'] ?? null;
    }
    public static function username() {
        return $_SESSION['username'] ?? null;
    }
    public static function email() {
        return $_SESSION['email'] ?? null;
    }
}