<?php
declare(strict_types=1);

namespace App\Security;

use App\Auth\Auth;

final class AccessControl
{
    public static function requireLogin(): void
    {
        if (!Auth::isAuthenticated()) {
            header('Location: ' . BASE_URL . '/login/index');
            exit;
        }
    }
}
