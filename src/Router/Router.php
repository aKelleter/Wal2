<?php
declare(strict_types=1);

namespace App\Router;

/**
 * Mini routeur pour une application simple sans framework.
 * Résout l'URL à partir de la requête et inclut la page correspondante.
 */
final class Router
{
    /**
     * Base de l'URL (chemin relatif à la racine du serveur).
     * Doit correspondre à la constante BASE_URL définie dans config/conf.php
     */
    public static string $basePath = BASE_URL;
    
    /**
     * Résout l'URL demandée en nom de page.
     * Exemple : "/git.docs/commandes" → "commandes"
     * Si vide : retourne "home"
     * 
     * @return string 
     */
    public static function resolve(): string
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $base = trim(self::$basePath, '/');

        // Comparaison insensible à la casse
        if (!empty($base) && stripos($uri, $base) === 0) {
            $uri = trim(substr($uri, strlen($base)), '/');
        }

        return $uri ?: DEFAULT_MODULE.DS.'index';
    }


    /**
     * Capture et retourne le contenu HTML de la page à afficher.
     * Si la page n'existe pas, retourne une erreur 404.
     * 
     * @return string 
     */
   

    public static function render(): string
    {
        $page = self::resolve(); // ex: "backend/admin"
        $file = ROOT_PATH . '/public/' . $page . '.php';

        // Empêche les tentatives de traversal
        if (str_contains($page, '..')) {
            http_response_code(400);
            return "<h1>".T_("Chemin non autorisé")."</h1>";
        }

        ob_start();
        if (file_exists($file)) {
            require $file;
        } else {
            http_response_code(404);
            // Version “page dédiée” (si /public/errors/404.php existe) :          
            $errorPage = ROOT_PATH . '/public/errors/404.php';
            //DEBUG//echo $errorPage;

            if (file_exists($errorPage)) {                
                require $errorPage;
                exit;
            } else {
                // Fallback :
                echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>Error 404</title></head><body>';
                echo '<h1>Error 404 – '.T_("Page introuvable.").'</h1>';
                echo '<p>'.T_("La page demandée n'hexiste pas.").'</p>';
                echo '</body></html>';
            }
        }
        return ob_get_clean();
    }   

}
