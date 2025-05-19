<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();

// CSRF-Token generieren, wenn nicht vorhanden
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../config/config.php';
// CSRF-Token generieren, wenn nicht vorhanden



// Autoloader für Models und Controller
spl_autoload_register(function($class) {
    foreach(['models', 'controllers'] as $folder) {
        $file = __DIR__ . "/../$folder/$class.php";
        if (file_exists($file)) require_once $file;
    }
});

require_once '../config/load_language.php';
loadLanguage();

// Adminpanel-Shortcuts
if (isset($_GET['a']) && $_GET['a'] === 'adminpanel') {
    if (!Session::isLoggedIn()) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
    include __DIR__ . '/../views/adminpanel.php';
    exit;
}
/*
if (isset($_GET['a']) && $_GET['a'] === 'assignGroupForm') {
    if (!Session::isLoggedIn()) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
    $userModel = new User();
    $groupModel = new Group();
    $users = $userModel->getAll();
    $groups = $groupModel->getAll();
    include __DIR__ . '/../views/assign_group_form.php';
    exit;
}

if (isset($_GET['a']) && $_GET['a'] === 'removeUserGroup' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::isLoggedIn()) {
        header('Location: index.php?c=Auth&a=login');
        exit;
    }
    $userModel = new User();
    $userModel->removeGroup($_POST['user_id'], $_POST['group_id']);
    header('Location: index.php?a=assignGroupForm');
    exit;
}

if (isset($_GET['a']) && $_GET['a'] === 'assignGroup' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();
    $userModel->assignGroup($_POST['user_id'], $_POST['group_id']);
    header('Location: index.php?a=assignGroupForm');
    exit;
}
*/






// Standard-MVC-Controller-Handling
$controller = $_GET['c'] ?? 'Auth';
$action = $_GET['a'] ?? 'login';

$controllerClass = "{$controller}Controller";
$controllerFile = __DIR__ . "/../controllers/{$controllerClass}.php";

// Controllerdatei explizit laden, falls Autoloader nicht greift
if (file_exists($controllerFile) && !class_exists($controllerClass)) {
    require_once $controllerFile;
}

if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
    $ctrl = new $controllerClass();
    $ctrl->$action();
} else {
    http_response_code(404);
    echo "<h2>404 Not Found</h2>";
    echo "<p>Controller <strong>$controllerClass</strong> oder Methode <strong>$action</strong> nicht gefunden.</p>";
}