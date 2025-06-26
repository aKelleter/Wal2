<?php
declare(strict_types=1);

namespace App\Core;

final class CacheManager
{
    public static function clear(string $env = ENV): bool
    {
        $dir = ROOT_PATH . '/cache/' . $env;

        if (!is_dir($dir)) {
            return false;
        }

        $files = glob($dir . '/*.html');
        if ($files === false) {
            return false;
        }

        foreach ($files as $file) {
            @unlink($file);
        }

        return true;
    }

    public static function clearPage(string $baseName): bool
    {
        $baseName = basename($baseName); // retire les chemins
        $baseName = preg_replace('/\.html$/', '', $baseName); // retire .html si présent

        $dir = self::getCacheDir();
        $pattern = $dir . '/' . $baseName . '*.html'; // ex: git_index*.html
        $files = glob($pattern);

        if (!$files) {
            return false;
        }

        $success = false;
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $success = true;
            }
        }

        return $success;
    }



    public static function getCacheDir(string $env = ENV): string
    {
        return ROOT_PATH . '/cache/' . $env;
    }

    /**
     * Lister tous les fichiers du cache de l'environnement passé en paramètre
     * Un filtre optionnel est applicable.
     *   Ex.: 
     *      // Liste uniquement les fichiers contenant "_frBE"
     *      CacheManager::listCacheFiles(ENV, '_frBE');
     * 
     * @param string $env 
     * @param null|string $filter 
     * @return array 
     */ 
    public static function listCacheFiles(string $env = ENV, ?string $filter = null): array
    {
        
        $env = preg_replace('/[^a-z]/', '', strtolower($env));
        $dir = self::getCacheDir($env);

        if (!is_dir($dir)) return [];

        $files = glob($dir . '/*.html');
        if (!is_array($files)) return [];

        $files = array_filter($files, 'is_readable');

        if ($filter) {
            $files = array_filter($files, fn($f) => str_contains(basename($f), $filter));
        }

        usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));

        return $files;
    }


    /**
     * Supprimer tous les caches dans toutes les langues du fichier passé en paramère
     * 
     * @param string $key 
     * @return int 
     */
    public static function clearAllLangsForPage(string $key): int
    {
        $dir = self::getCacheDir();
        $pattern = $dir . '/' . preg_quote($key, '/') . '_*.html';
        $files = glob($pattern);
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Supprimer tous les caches de la langue passée en paramètre uniquement
     *  Utilisation:
     *      $nb = CacheManager::clearByLang('fr_BE');
     *      echo "$nb fichiers supprimés.";
     * 
     * @param string $lang  // ex.: 'fr_BE', 'en_US', 'fr_FR',...
     * @param string $env 
     * @return int 
     */
    public static function clearByLang(string $lang, string $env = ENV): int
    {
        $dir = self::getCacheDir($env);

        // Sécurise le nom de la langue (fr_BE → frBE, etc.)
        $lang = str_replace('-', '', $lang); // ex: fr-BE → frBE
        $lang = preg_replace('/[^a-zA-Z0-9_]/', '', $lang);

        // Recherche tous les fichiers de cache correspondant à cette langue
        $pattern = $dir . '/*_' . $lang . '.html';
        $files = glob($pattern) ?: [];

        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $deleted++;
            }
        }

        return $deleted;
    }



}
