<?php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function unset($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    public static function get($key) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = array();
    }

    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['id']) && !empty($_SESSION['id']);
    }

    public static function requireLogin() {
        self::start();
        if (!self::isLoggedIn()) {
            header('Location: ../../login.php');
            exit();
        }
    }

    public static function requireAdmin() {
        self::start();
        if (!self::isLoggedIn() || self::get('role') !== 'admin') {
            header('Location: ../../login.php');
            exit();
        }
    }

    public static function setFlash($type, $message) {
        self::start();
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash($type) {
        self::start();
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
}
