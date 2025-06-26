<?php
declare(strict_types=1);

namespace App\Module\Default;

use App\UI\Template;
use App\UI\Layout;

final class DefaultController
{
    /**
     * Contrôleur de la page public/git/index.php
     * 
     * @return void 
     */
    public static function index(): void
    {
        $pageTitle = 'WaL-2';

        // -----------------------------------------------
        $tpl = new Template(ROOT_PATH . '/templates/default');
        $tpl->setFile([
                'main'  => 'index.html'    
        ]);

        // Blocs
        $tpl->setVar('Header', Layout::getHeader($pageTitle));
        $tpl->setVar('SectionHeader', Layout::getSectionHeader());
        $tpl->setVar('Navigation', Layout::getNavigation());
        $tpl->setVar('BtnTop', Layout::getBtnTop());
        $tpl->setVar('AppName', APP_NAME);
        $tpl->setVar('Footer', Layout::getFooter());
        $tpl->setVar('JSSection', Layout::getJSSection());

        // Chaines de traduction spécifiques à la page
        $tpl->setVar('STR_PRESENTATION', T_("est une application PHP modulaire sans framework, conçue autour des principes suivants :
            <ul>
                <li>Front Controller unique</li>
                <li>Moteur de template maison</li>
                <li>Routage personnalisé</li>
                <li>Gestion de l’internationalisation (I18n) avec Gettext</li>
                <li>Système d’erreurs et de cache intégrés</li>
            </ul>
            Ce projet est pensé pour être facilement réutilisable, découpé en modules, et personnalisable.
        "));
        $tpl->setVar('STR_PROJET', T_("Il constitue un socle léger, clair et modulaire, idéal pour des prototypes ou des bases de projets professionnels structurés.")); 
        $tpl->setVar('STR_PROFITEZ', T_("Profitez-en bien !"));

        // Inclusion des chaînes de caractères à traduire
        require_once ROOT_PATH . '/locale/strToTranslate.php';

        $tpl->pparse('display', 'main');
    }

    public static function page(array $datas): void
    {
        $pageTitle = 'WaL-2 - '.$datas['title'];

        // -----------------------------------------------
        $tpl = new Template(ROOT_PATH . '/templates/default');
        $tpl->setFile([
                'main'  => 'page.html'    
        ]);

        // Blocs
        $tpl->setVar('Header', Layout::getHeader($pageTitle));
        $tpl->setVar('SectionHeader', Layout::getSectionHeader());
        $tpl->setVar('Navigation', Layout::getNavigation());
        $tpl->setVar('BtnTop', Layout::getBtnTop());
        $tpl->setVar('Content', $datas['content']);
        $tpl->setVar('AppName', APP_NAME);
        $tpl->setVar('Footer', Layout::getFooter());
        $tpl->setVar('JSSection', Layout::getJSSection());
       
        // Inclusion des chaînes de caractères à traduire
        require_once ROOT_PATH . '/locale/strToTranslate.php';

        $tpl->pparse('display', 'main');
    }
}