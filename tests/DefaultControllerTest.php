<?php
/**
 * Exécuter dans un terminal à la racine du projet : * 
 * php .\tools\phpunit.phar --testdox tests
 */

use PHPUnit\Framework\TestCase;

require_once 'bootstrap.php';
require_once 'src/Module/Default/DefaultController.php';

class DefaultControllerTest extends TestCase
{
    public function testIndexMethodExists()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertTrue(method_exists('App\Module\Default\DefaultController', 'index'));
    }

    public function testPageMethodExists()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertTrue(method_exists('App\Module\Default\DefaultController', 'page'));
    }

    // Exemple de test de comportement (à adapter selon votre logique)
    public function testPageWithSampleData()
    {
        $datas = [
            'title' => 'Test',
            'content' => 'Ceci est un test.'
        ];
        
        // Comme la méthode page() affiche directement le résultat,
        // on peut tester qu'elle ne lève pas d'exception
        $this->expectNotToPerformAssertions();
        \App\Module\Default\DefaultController::page($datas);
    }
}
