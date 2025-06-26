<?php
declare(strict_types=1);

namespace App\UI;

use App\I18n\I18n;
use App\UI\Assets;

final class Layout
{
 
    /**
     * Retourne l'entête HTML de la page
     * `
     * @param string $path 
     * @param string $title 
     * @return string 
     */
    public static function getHeader(string $title = '') : string
    {
        $base_url = BASE_URL;
        $html = <<<HTML
        <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">

        HTML;
        
        $html .= Assets::renderPreloads();       
        $html .= Assets::renderStyles();
        
        $html .= '</head>';
        return $html;
    }
    
    /**
     * Retourne la section header de l'application
     * 
     * @param string $path 
     * @param string $subtitle 
     * @return string 
     */
    public static function getSectionHeader(string $path = './', string $subtitle = ''): string
    {
        $app_name = APP_NAME;
        $html_subtitle = !empty($subtitle) ? "<h2 class=\"text-white\"> - $subtitle - </h2>" : '';

        return <<<HTML
        <header class="py-5 mb-4 text-center header-custom">
            <h1 class="display-5 fw-bold mb-0 text-white">$app_name</h1>
            $html_subtitle
        </header>
        HTML;
    }

    /**
     * Retourne la section footer de l'application
     * 
     * @param string $path 
     * @return string 
     */
    public static function getFooter(string $path = './'): string
    {
        $app_name = APP_NAME;
        $version = APP_VER;

        return <<<HTML
        <footer class="text-center text-secondary py-4">
            $app_name - $version
        </footer>
        HTML;
    }

    /**
     * Construction de l'url des langues
     * 
     * @param string $url 
     * @param string $lang 
     * @return string 
     */
    private static function buildLangUrl(string $url, string $lang): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $query = [];
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $query);
        }
        $query['lang'] = $lang;
        return $path . '?' . http_build_query($query);
    }


    /**
     * Retourne le menu de navigation principal de l'application
     * 
     * @return string 
     */
    public static function getNavigation(): string
    {
        $menu = require ROOT_PATH . '/config/menu.php';
        $base_url = BASE_URL;
        $isAuth = isset($_SESSION['user']);

        $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $base = trim($base_url, '/');
        if (!empty($base) && str_starts_with($current, $base)) {
            $current = trim(substr($current, strlen($base)), '/');
        }

        $html = '<nav class="navbar navbar-expand-md navbar-blue shadow-sm mb-4 rounded-3 shadow-sm">';
        $html .= '<div class="container">';
        $html .= '<ul class="nav nav-pills justify-content-center">';

        foreach ($menu as $key => $entry) {
            // -- Dropdown (menu groupé) ?
            if (is_array($entry) && !array_key_exists('label', $entry)) {
                $dropdownItems = '';
                foreach ($entry as $subUrl => $subData) {
                    if (!is_array($subData) || !isset($subData['label'])) continue;
                    $auth = $subData['auth'] ?? null;
                    if ($auth !== null && $auth !== $isAuth) continue;

                    $active = ($current === $subUrl) ? ' active link-orange' : '';
                    $dropdownItems .= '<li><a class="dropdown-item' . $active . '" href="' . $base_url . '/' . $subUrl . '">' . htmlspecialchars($subData['label']) . '</a></li>';
                }

                if (!empty($dropdownItems)) {
                    $html .= '<li class="nav-item dropdown">';
                    $html .= '<a class="nav-link dropdown-toggle" href="#" id="dropdown-' . htmlspecialchars((string)$key) . '" data-bs-toggle="dropdown" aria-expanded="false">' . htmlspecialchars((string)$key) . '</a>';
                    $html .= '<ul class="dropdown-menu" aria-labelledby="dropdown-' . htmlspecialchars((string)$key) . '">';
                    $html .= $dropdownItems;
                    $html .= '</ul></li>';
                }
            }

            // -- Lien simple enrichi
            elseif (is_array($entry) && isset($entry['label'])) {
                $auth = $entry['auth'] ?? null;
                if ($auth !== null && $auth !== $isAuth) continue;

                $active = ($current === $key) ? ' active link-orange' : '';
                $html .= '<li class="nav-item">';
                $html .= '<a class="nav-link' . $active . '" href="' . $base_url . '/' . $key . '">' . htmlspecialchars($entry['label']) . '</a>';
                $html .= '</li>';
            }
        }

        // -- Ajouter le lien logout si connecté
        if ($isAuth) {
            $html .= '<li class="nav-item">';
            $html .= '<a class="nav-link text-warning" href="' . $base_url . '/login/logout">' . T_('Déconnexion') . '</a>';
            $html .= '</li>';
        }

        // -- Sélecteur de langue dynamique
        if(APP_MULTI_LANG)
        {
            $langs = I18n::getSupported();
            //DEBUG//var_dump($langs); 
            $currentLang = I18n::getLang();
            //DEBUG//echo '- CURRENT LANG : '. $currentLang;
            $currentUrl = $_SERVER['REQUEST_URI'];
            //DEBUG//echo '- CURRENT URL : '. $currentUrl;

            $html .= '<li class="nav-item dropdown ms-2">';
            $html .= '<a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">'
                . strtoupper(htmlspecialchars($langs[$currentLang])) . '</a>';
            $html .= '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">';
            foreach ($langs as $string => $text) {
                $active = ($string === $currentLang) ? 'active' : '';
                $html .= '<li><a class="dropdown-item ' . $active . '" href="' . htmlspecialchars(self::buildLangUrl($currentUrl, $string)) . '">' . strtoupper($text) . '</a></li>';
            }
            $html .= '</ul></li>';
        }
        
    
      

        $html .= '</ul></div></nav>';
        return $html;
    }


    /**
     * Retourne les liens de ressources de la page passée en paramètre
     * 
     * @param string $page 
     * @return string 
     */
    public static function getRessources(string $page) : string 
    {
        switch($page)
        {
            case 'branches' :
                return <<<HTML
                <div class="callout mb-4">      
                    <div>
                        <h6>Sources</h6>
                        <ul>
                            <li><a href="https://kinsta.com/fr/base-de-connaissances/git-delete-branche-locale/" target="_blank" rel="noopener">Kinsta : supprimer une branche locale</a></li>
                            <li><a href="https://openclassrooms.com/fr/courses/7162856-gerez-du-code-avec-git-et-github/7475886-apprehendez-le-systeme-de-branches" target="_blank" rel="noopener">OpenClassrooms : appréhender le système de branches</a></li>
                            <li><a href="https://git-scm.com/book/fr/v2/Les-branches-avec-Git-Les-branches-en-bref" target="_blank" rel="noopener">Git SCM : branches en bref</a></li>            
                            <li><a class="mb-3" target="_blank" href="https://www.atlassian.com/fr/git/tutorials/using-branches">Atlassian (les branches)</a> </li>            
                        </ul>            
                    </div>
                </div>
                HTML;
                break;

            default :
                return '';
        }
    }

    /**
     * Retourne la liste des script JS à charger
     * 
     * @param string $path 
     * @return string 
     */
    public static function getJSSection(string $path = './') : string 
    {
        $base_url = BASE_URL;
        $html = Assets::renderScripts();       
        return $html;
    }

    public static function getBtnTop() :string
    {
        return '<button id="btn-top" class="btn btn-primary rounded-circle shadow"
        style="position:fixed; bottom:2rem; left:calc(50% + 740px); display:none; z-index:1030;">
        ↑
    </button>
        ';
    }
    
    /**
     * Génère une div d’alerte Bootstrap
     * 
     * Utilisations:
     * Layout::alert("Votre compte a été créé !", "success");
     * Layout::alert("Nom d'utilisateur ou mot de passe invalide.", "danger");
     * Layout::alert("Vous pouvez fermer cette alerte.", "warning", true); // fermable
     * Layout::alert("Ceci est une info non-fermeture.", "info", false);   // non fermable
     * 
     * @param string $message Le texte à afficher
     * @param string $type    Type : 'success', 'danger', 'warning', 'info', 'primary', etc.
     * @param bool $dismissible Permettre la fermeture par l'utilisateur (optionnel)
     * @return string HTML de l’alerte
     */
    public static function alert(string $message, string $type = 'info', bool $dismissible = true): string
    {
        $types = ['primary','secondary','success','danger','warning','info','light','dark'];
        if (!in_array($type, $types)) {
            $type = 'info';
        }
        $closeBtn = '';
        $dismissClass = '';
        if ($dismissible) {
            $dismissClass = ' alert-dismissible fade show';
            $closeBtn = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>';
        }
        return sprintf(
            '<div class="alert alert-%s%s text-center" role="alert">%s%s</div>',
            htmlspecialchars($type),
            $dismissClass,
            htmlspecialchars($message),
            $closeBtn
        );
    }

    /**
     * Génère une div d'information ou de message permanent
     * 
     * Utilisation :
     *   echo Layout::message("Bienvenue sur la page d’accueil !", "info");
     *   echo Layout::message("Bravo, inscription réussie !", "success", "text-center");
     * 
     * @param string $content  Texte à afficher (HTML autorisé)
     * @param string $type     Type Bootstrap (info, success, warning, danger, etc.) 
     * @param string $extraClass Classe(s) supplémentaire(s)
     * @return string
     */
    public static function message(
        string $content,
        string $type = 'info',        
        string $extraClass = ''
    ): string
    {
        $class = 'div-message bg-' . $type . ' border border-' . $type . ' rounded p-3 mb-3 ' . $extraClass;
        $html = '<div class="' . htmlspecialchars($class) . '" role="status">';
        $html .= $content . '</div>';
        return $html;
    }
   
}
