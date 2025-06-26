<?php
declare(strict_types=1);

namespace App\UI;


final class View
{
    /**
     * Redirige vers une URL donnée
     *
     * @param string $url URL absolue ou relative
     * @param int $statusCode Code HTTP (302 par défaut)
     * @return never
     */
    public static function redirect(string $url, int $statusCode = 302): never
    {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    /**
     * Stocke un message flash puis redirige
     * Exemple :View::redirectWithMessage(BASE_URL . '/article', 'Article supprimé avec succès.');
     *
     * @param string $url URL de redirection
     * @param string $message Message à afficher
     * @param int $statusCode Code HTTP (302 par défaut)
     * @return never
     */
    public static function redirectWithMessage(string $url, string $message, int $statusCode = 302): never
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['flash_message'] = $message;
        self::redirect($url, $statusCode);
    }

    /**
     * Récupère et supprime un message flash
     *
     * @return string
     */
    public static function consumeFlashMessage(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!empty($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }

        return '';
    }

    /**
     * Sécurise une chaîne de caractères pour l'affichage HTML
     *
     * @param string $text
     * @return string
     */
    public static function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}