<?php
class AdminPanelController {
    public function index() {
        Session::start();
        if (!Session::isLoggedIn()) {
            header('Location: index.php?c=Auth&a=login');
            exit;
        }
        include __DIR__ . '/../views/adminpanel.php';
    }
}