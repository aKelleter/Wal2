# Git.Docs – Guide de démarrage

_Ce document présente le fonctionnement interne du projet **Git.Docs**, destiné à servir de base de démarrage (bootstrap) pour d'autres projets PHP modulaires._

**Version générée le 26/06/2025 à 09:24**

---

## 1. Présentation générale

Git.Docs est une application PHP modulaire sans framework, conçue autour des principes suivants :

- Front Controller unique
- Moteur de template maison
- Routage personnalisé
- Gestion de l’internationalisation (I18n) avec Gettext
- Système d’erreurs et de cache intégrés

Ce projet est pensé pour être facilement réutilisable, découpé en modules, et personnalisable.

---

## 2. Fonctionnement général

Le fichier d'entrée principal est `public/index.php`. Il initialise le bootstrap, configure les assets globaux, puis appelle le routeur (`Router::render()`).

Le fichier `bootstrap.php` charge :
- les constantes de configuration (`conf.php`)
- la connexion à la base de données (`Connection::init`)
- le système d'authentification (`Auth::init`)
- la langue via `I18n::init()`
- la gestion des erreurs via `ErrorHandler::register()`

Le moteur de template (`App\UI\Template`) permet d’afficher des fichiers HTML avec des blocs et des variables dynamiques. Il est compatible avec le cache.

---

## 3. Classes principales et rôles

- **App\Router\Router** : Gère les routes de l'application et résout le contrôleur à appeler.
- **App\UI\Template** : Moteur de template léger (setFile, setVar, parse, pparse…).
- **App\UI\Layout** : Génère les blocs d’interface standard (Header, Footer, etc.).
- **App\UI\Assets** : Ajoute dynamiquement les fichiers CSS et JS globaux ou contextuels.
- **App\Core\ErrorHandler** : Gère les erreurs PHP, exceptions et logs.
- **App\I18n\I18n** : Initialise et configure Gettext pour les traductions.
- **App\Auth\Auth** : Système d’authentification (login, sessions).
- **App\Security\Csrf** : Génère et vérifie les jetons CSRF.
- **App\Security\Security** : Nettoyage des données d’entrée (`sanitizeInput`, `sanitizeArray`).
- **App\Core\CacheManager** : Gère les fichiers de cache HTML.
- **App\Helpers\Helpers** : Fonctions utilitaires (ex : Gettext, formatting).
- **App\Database\Connection** : Initialise la connexion PDO partagée.

---

## 4. Utiliser Git.Docs comme base pour un nouveau projet

### Étapes conseillées :

1. **Copier le projet** dans un nouveau dossier.
2. **Renommer le module `git/`** par votre besoin : `blog/`, `admin/`, etc.
3. **Adapter les templates** HTML dans `/templates`.
4. **Modifier les routes** et leurs comportements dans `Router.php`.
5. **Configurer la base de données** dans `config/database.php`.
6. **Ajouter vos propres classes** dans `src/`.
7. **Configurer vos traductions** dans `/locale/` avec `.po/.mo`.
8. **Gérer le cache et les erreurs** grâce aux classes dédiées.

Ce projet constitue un socle léger, clair et modulaire, idéal pour des prototypes ou des bases de projets professionnels structurés.

---

