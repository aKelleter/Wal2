# Tests automatisés avec PHPUnit



## 1. Mode opératoire



Le dossier ***tests/*** à la racine du projet est prévu pour stocker les classes de tests.
Créer vos fichiers dans ce répertoire. Vos fichiers contiendront des classes de test qui vérifient le comportement de vos contrôleurs ou classes.

Lancez **PHPUnit** pour exécuter vos tests et vérifier que tout fonctionne. 
La version actuelle de **PHPUnit** est la  **12.2.5**.

Exemple  (exécutez cette commande à la racine du projet) :

```shell
 php .\tools\phpunit.phar --testdox tests
```

## 2. Exemple

Un exemple de test a été généré dans le fichier : ***DefaultControllerTest.php***. 

Il vérifie l’existence des méthodes **index** et **page** dans votre contrôleur, et propose un test simple d’appel de la méthode **page()**.



```php
<?php
/**
 * Exécuter dans un terminal à la racine du projet : 
 * php .\tools\phpunit.phar --testdox tests*
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
```



## 3. Apprentissage des tests unitaires et de PHPUnit

### Liens :

Documentation pour PHPUnit 12.2 : 
https://docs.phpunit.de/en/12.2/



La page d'accueil du projet :
https://phpunit.de/index.html

