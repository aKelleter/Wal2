<?php
declare(strict_types=1);

namespace App\I18n;

use Gettext\Loader\MoLoader;
use Gettext\Translations;

final class I18n
{
    /** @var array<string,string> */
    private static array $supported = ['fr_BE' => 'Fr', 'en_US' => 'En', 'nl_BE' => 'Nl'];
    private static string $domain = 'messages';
    private static string $localeDir;
    private static string $lang = 'fr_BE';
    /** @var ?Translations */
    private static ?Translations $translations = null;

    public static function init(?string $lang = null): void
    {
        self::$localeDir = defined('ROOT_PATH')
            ? ROOT_PATH . '/locale'
            : __DIR__ . '/../../locale';

        // 1. Forçage GET
        if ($lang === null && isset($_GET['lang']) && array_key_exists($_GET['lang'], self::$supported)) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
        }

        // 2. Session existante
        if ($lang === null && isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], self::$supported)) {
            $lang = $_SESSION['lang'];
        }

        // 3. Détection langue navigateur
        if ($lang === null && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($browserLangs as $bl) {
                $bl = strtolower(trim(explode(';', $bl)[0]));
                // Cherche correspondance exacte (fr_BE, en_US, nl_BE…)
                foreach (array_keys(self::$supported) as $supported) {
                    if (strtolower($supported) === $bl || str_replace('-', '_', $bl) === strtolower($supported)) {
                        $lang = $supported;
                        break 2;
                    }
                }
                // Cherche correspondance partielle (fr → fr_BE, en → en_US, nl → nl_BE…)
                if (strlen($bl) === 2) {
                    foreach (array_keys(self::$supported) as $supported) {
                        if (stripos($supported, $bl . '_') === 0) {
                            $lang = $supported;
                            break 2;
                        }
                    }
                }
            }
        }

        // 4. Fallback défaut
        if ($lang === null || !array_key_exists($lang, self::$supported)) {
            $lang = 'fr_BE';
        }

        self::$lang = $lang;
        $_SESSION['lang'] = $lang;

        // Chargement du fichier MO via la librairie PHP Gettext
        $moPath = self::$localeDir . '/' . $lang . '/LC_MESSAGES/' . self::$domain . '.mo';
        if (file_exists($moPath)) {
            $loader = new \Gettext\Loader\MoLoader();
            self::$translations = $loader->loadFile($moPath);
        } else {
            self::$translations = null;
        }
    }


    /** Retourne la langue courante */
    public static function getLang(): string
    {
        return self::$lang;
    }

    /** Liste des langues disponibles */
    public static function getSupported(): array
    {
        return self::$supported;
    }

    /** Traduction simple */
    public static function t(string $msg): string
    {
        if (self::$translations) {
            $tr = self::$translations->find(null, $msg);
            if ($tr && ($text = $tr->getTranslation())) {
                return $text;
            }
        }
        return $msg;
    }

    /** Traduction avec pluriel */
    public static function tp(string $singular, string $plural, int $count): string
    {
        if (self::$translations) {
            $tr = self::$translations->find(null, $singular);
            if ($tr) {
                $pluralIndex = ($count == 1 ? 0 : 1); // Adapté pour la majorité des langues occidentales
                $translated = $tr->getTranslation($pluralIndex);
                if ($translated !== null && $translated !== '') {
                    return $translated;
                }
            }
        }
        // Fallback si aucune traduction trouvée
        return $count === 1 ? $singular : $plural;
    }


}
