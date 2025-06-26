<?php
namespace App\Security;

use Random\RandomException;

final class Csrf
{
    /**
     * Génère un jeton 
     * 
     * @return string 
     * @throws RandomException 
     */
    public static function generateToken(): string
    {
        // Démarre la session si elle n'est pas déjà active
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        // Génère le token s'il n'existe pas
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le jeton
     * 
     * @param string $token 
     * @return bool 
     */
    public static function verifyToken(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Méthode utilitaire à appeler au début de chaque traitement POST
     * 
     * @return void 
     */
    public static function requireValidToken(): void
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            (
                !isset($_POST['csrf_token']) ||
                !self::verifyToken($_POST['csrf_token'])
            )
        ) {
            http_response_code(400);
            die('Erreur de sécurité : CSRF token invalide.');
        }
    }

    public static function requireValidTokenFromGet(): void
    {
        if (
            $_SERVER['REQUEST_METHOD'] === 'GET' &&
            (
                !isset($_GET['token']) ||
                !self::verifyToken($_GET['token'])
            )
        ) {
            http_response_code(400);
            die('Erreur de sécurité : CSRF token manquant ou invalide.');
        }
    }
    

}
