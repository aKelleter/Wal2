<?php
namespace App\Module\Article;

use App\UI\Template;
use App\UI\Layout;
use App\Security\AccessControl;
use App\Security\Csrf;
use App\UI\Url;
use App\View\SampleArticleView;
use App\Model\SampleArticleModel;
use App\UI\View;


final class ArticleController
{
    public static function index(): void
    {
        AccessControl::requireLogin();
        $pageTitle = 'Liste des articles';
        $message = View::consumeFlashMessage();
        $csrfToken = Csrf::generateToken();

        $articles = SampleArticleModel::getAll(); // retourne un tableau d’objets ou d’arrays

        $tpl = new Template(ROOT_PATH . '/templates/article');
        $tpl->setFile(['main' => 'index.html']);
        $tpl->setVar([
            'Header'        => Layout::getHeader($pageTitle),
            'SectionHeader' => Layout::getSectionHeader(),
            'Navigation'    => Layout::getNavigation(),
            'ArticlesTable' => SampleArticleView::renderList(),
            'CsrfToken'     => $csrfToken,
            'Message'       => $message,
            'UrlNew'        => Url::format('/article/create'),
            'Footer'        => Layout::getFooter(),
            'JSSection'     => Layout::getJSSection(),
        ]);
        $tpl->pparse('display', 'main');
    }

    public static function show(int $id): void
    {
        AccessControl::requireLogin();
        $article = SampleArticleModel::find($id);
        if (!$article) {
            View::redirectWithMessage(BASE_URL . '/article', '<div class="alert alert-warning">Article introuvable.</div>');
        }

        $pageTitle = 'Détail de l’article';
        $tpl = new Template(ROOT_PATH . '/templates/article');
        $tpl->setFile(['main' => 'show.html']);
        $tpl->setVar([
            'Header'        => Layout::getHeader($pageTitle),
            'SectionHeader' => Layout::getSectionHeader(),
            'Navigation'    => Layout::getNavigation(),
            'ArticleDetail' => SampleArticleView::renderDetail($article),
            'UrlRetour'     => Url::format('/article'),
            'Footer'        => Layout::getFooter(),
            'JSSection'     => Layout::getJSSection(),
        ]);
        $tpl->pparse('display', 'main');
    }

    public static function create(): void
    {
        AccessControl::requireLogin();
        $pageTitle = 'Nouvel article';
        $csrfToken = Csrf::generateToken();
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::requireValidToken();
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';

            if ($title && $content) {
                SampleArticleModel::insert($title, $content);
                View::redirectWithMessage(BASE_URL . '/article', '<div class="alert alert-success">Article ajouté.</div>');
            } else {
                $message = '<div class="alert alert-danger">Champs obligatoires manquants.</div>';
            }
        }

        $tpl = new Template(ROOT_PATH . '/templates/article');
        $tpl->setFile(['main' => 'form.html']);
        $tpl->setVar([
            'Header'        => Layout::getHeader($pageTitle),
            'SectionHeader' => Layout::getSectionHeader(),
            'Navigation'    => Layout::getNavigation(),
            'FormAction'    => Url::format('/article/create'),
            'CsrfToken'     => $csrfToken,
            'Message'       => $message,
            'ArticleTitle'  => $_POST['title'] ?? '',
            'ArticleContent'=> $_POST['content'] ?? '',
            'Footer'        => Layout::getFooter(),
            'JSSection'     => Layout::getJSSection(),
        ]);
        $tpl->pparse('display', 'main');
    }

    public static function edit(int $id): void
    {
        AccessControl::requireLogin();
        $article = SampleArticleModel::find($id);
        if (!$article) {
            View::redirectWithMessage(BASE_URL . '/article', '<div class="alert alert-warning">Article introuvable.</div>');
        }

        $csrfToken = Csrf::generateToken();
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::requireValidToken();
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';

            if ($title && $content) {
                SampleArticleModel::update($id, $title, $content);
                View::redirectWithMessage(BASE_URL . '/article', '<div class="alert alert-success">Article mis à jour.</div>');
            } else {
                $message = '<div class="alert alert-danger">Champs obligatoires manquants.</div>';
            }
        }

        $tpl = new Template(ROOT_PATH . '/templates/article');
        $tpl->setFile(['main' => 'form.html']);
        $tpl->setVar([
            'Header'         => Layout::getHeader('Modifier article'),
            'SectionHeader'  => Layout::getSectionHeader(),
            'Navigation'     => Layout::getNavigation(),
            'FormAction'     => Url::format("/article/edit/$id"),
            'CsrfToken'      => $csrfToken,
            'Message'        => $message,
            'ArticleTitle'   => $article['title'],
            'ArticleContent' => $article['content'],
            'Footer'         => Layout::getFooter(),
            'JSSection'      => Layout::getJSSection(),
        ]);
        $tpl->pparse('display', 'main');
    }

    public static function delete(int $id): void
    {
        AccessControl::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::requireValidToken();
            SampleArticleModel::delete($id);
            View::redirectWithMessage(BASE_URL . '/article', '<div class="alert alert-success">Article supprimé.</div>');
        }

        View::redirectWithMessage(BASE_URL . '/article', '<div class="alert alert-warning">Méthode invalide.</div>');
    }
}
