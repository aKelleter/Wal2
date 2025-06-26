<?php
declare(strict_types=1);

namespace App\UI;

final class Assets
{
    private static array $scripts = [];
    private static array $styles = [];
    private static array $preloads = [];

    /**
     * Ajoute le script à la propriété $scripts
     * 
     * @param string $src 
     * @param bool $defer  Ajoute l’attribut defer
     * @param bool $module  true = ajoute type="module"
     * @param bool $async   Ajoute l’attribut async
     * @return void 
     */
    public static function addScript(string $src, bool $defer = true, bool $module = false, bool $async = false): void
    {
        // Empêche les doublons exacts
        foreach (self::$scripts as $sc) {
            if (
                $sc['src']    === $src &&
                $sc['defer']  === $defer &&
                $sc['module'] === $module &&
                $sc['async']  === $async
            ) return;
        }
        self::$scripts[] = [
            'src'    => $src,
            'defer'  => $defer,
            'module' => $module,
            'async'  => $async,
        ];
    }

    /**
     * Retourne le code HTML des scripts
     * 
     * @return string 
     */
    public static function renderScripts(): string
    {
        $html = '';
        foreach (self::$scripts as $sc) {
            $attrs = 'src="' . htmlspecialchars($sc['src']) . '"';
            if ($sc['defer'] && !$sc['module']) $attrs .= ' defer';
            if ($sc['module']) $attrs .= ' type="module"';
            $html .= "<script $attrs></script>\n";
        }
        return $html;
    }

    /**
     * Ajoute le CSS à la propriété $styles
     * 
     * @param string $href 
     * @return void 
     */
    public static function addStyle(string $href): void
    {
        if (!in_array($href, self::$styles)) {
            self::$styles[] = $href;
        }
    }

    /**
     * Retourne le code HTML des liens CSS
     * 
     * @return string 
     */
    public static function renderStyles(): string
    {
        $html = "<!-- Feuille de styles classique obligatoire --> \n";
        foreach (self::$styles as $href) {
            $html .= '<link rel="stylesheet" href="' . htmlspecialchars($href) . '">' . "\n";
        }
        return $html;
    }

    // --- PRELOAD ---

    /**
     * Ajoute un style en "preload" très efficace pour le CSS critique et JS crucial.
     * 
     * @param string $href 
     * @param string $as 'style' or 'script"
     * @return void 
     */
    public static function addPreload(string $href, string $as): void
    {
        foreach (self::$preloads as $pl) {
            if ($pl['href'] === $href && $pl['as'] === $as) return;
        }
        self::$preloads[] = [
            'href' => $href,
            'as'   => $as,
        ];
    }

    /**
     *  Retourne le code HTML des preloads
     * 
     * @return string 
     */
    public static function renderPreloads(): string
    {
        $html = "<!-- Preload en premier (peut améliorer le rendu initial sur réseau lent) --> \n";
        foreach (self::$preloads as $pl) {
            if ($pl['as'] === 'style') {
                $html .= '<link rel="preload" href="' . htmlspecialchars($pl['href']) . '" as="style" onload="this.rel=\'stylesheet\'">' . "\n";
                $html .= '<noscript><link rel="stylesheet" href="' . htmlspecialchars($pl['href']) . '"></noscript>' . "\n";
            } else {
                $html .= '<link rel="preload" href="' . htmlspecialchars($pl['href']) . '" as="' . htmlspecialchars($pl['as']) . "\">\n";
            }
        }
        return $html;
    }
}