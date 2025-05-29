<?php
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

function getSiteName()
{
    return 'UniFeed';
}

function getSiteUrl()
{
    return 'http://localhost/BullBoard';
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function redirectTo($path): never
{
    header("Location: " . getSiteUrl() . "/" . $path);
    exit();
}

function sanitizeInput($data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function setFlashMessage($type, $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage(): mixed
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>