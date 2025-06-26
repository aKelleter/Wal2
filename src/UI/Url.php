<?php
declare(strict_types=1);

namespace App\UI;

final class Url
{
    /**
     * Formatte une URL à partir d’un chemin et de paramètres éventuels
     * 
     *      Utilisation :
     *      - /users/profile?lang=en&id=42
     *            echo Url::format('/users/profile', ['lang'=>'en', 'id'=>42]);
     *      - Avec base URL absolue : https://monsite.com/users/profile?lang=en
     *            echo Url::format('/users/profile', ['lang'=>'en'], true);  
     *      - Juste le chemin nettoyé
     *            echo Url::format('////foo//bar/'); // /foo/bar
     * 
     * 
     * @param string $path    Chemin relatif ou absolu (ex : "/users/list")
     * @param array  $params  Tableau associatif de paramètres GET (ex : ['lang'=>'fr', 'id'=>3])
     * @param bool   $absolute Si vrai, ajoute BASE_URL devant (sinon, URL relative)
     * @return string
     */
    public static function format(string $path, array $params = [], bool $absolute = false, string $anchor = ''): string
    {
        // Nettoie les slashs (1 seul / entre chaque morceau)
        $path = preg_replace('#/+#', '/', '/' . ltrim($path, '/'));

        // Ajoute paramètres GET si fournis
        $query = !empty($params) ? '?' . http_build_query($params) : '';

        // Préfixe absolu si demandé
        $base = $absolute && defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';

        // Gestion de l'ancre de l'URL
        $anchor = $anchor !== '' ? '#' . ltrim($anchor, '#') : '';
        
        // Retourne l'URL complète
        return $base . $path . $query . $anchor;
    }
}
