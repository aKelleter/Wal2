<?php
declare(strict_types=1);

namespace App\Module\Login;

use App\UI\Template;
use App\UI\Layout;
use App\Auth\Auth;
use App\Security\Csrf;
use App\UI\View;
use RuntimeException;
use PDOException;
use Random\RandomException;

final class LoginController 
{
    /**
     * Contrôleur de la page public/login/index.php
     * 
     * @return void 
     * @throws RuntimeException 
     * @throws PDOException 
     * @throws RandomException 
     */
    public static function index(): void
    {
        $error = '';

        // Protéger automatiquement tout POST
        Csrf::requireValidToken();

        // Gestion du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Auth::login($_POST['username'], $_POST['password'])) {
                $error = Layout::alert(T_("Nom d'utilisateur ou mot de passe invalide."), "danger", false);
            } else {

                View::redirect(BASE_URL . '/adm/index');
                exit;
            }
        }

        $pageTitle = 'Git.Docs - Identification';
        $token_csrf = htmlspecialchars(Csrf::generateToken());

        // -----------------------------------------------
        $tpl = new Template(ROOT_PATH . '/templates/login');
        $tpl->setFile([
                'main'  => 'index.html'    
        ]);

        $tpl->setVar('Header', Layout::getHeader($pageTitle));
        $tpl->setVar('SectionHeader', Layout::getSectionHeader());
        $tpl->setVar('Navigation', Layout::getNavigation());
        $tpl->setVar('Error', $error);
        $tpl->setVar('Token_csrf', $token_csrf);
        $tpl->setVar('Footer', Layout::getFooter());
        $tpl->setVar('JSSection', Layout::getJSSection());

        // Inclusion des chaînes de caractères à traduire
        require_once ROOT_PATH . '/locale/strToTranslate.php';

        $tpl->pparse('display', 'main');

    }
}