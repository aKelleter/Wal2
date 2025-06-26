<?php
declare(strict_types=1);

namespace App\Auth;

use PDO;


final class Auth
{
    private static ?PDO $pdo = null;

    public static function init(PDO $pdo): void
    {
        self::$pdo = $pdo;
        session_start();
    }

    public static function login(string $username, string $password): bool
    {
        if (!self::$pdo) {
            throw new \RuntimeException('PDO non initialisé');
        }

        $stmt = self::$pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username']
            ];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
    }

    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}
