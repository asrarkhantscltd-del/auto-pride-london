<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireAuth(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}
