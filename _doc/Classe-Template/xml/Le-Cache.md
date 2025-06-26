Si on veut forcer le cache même en dev, on peut utiliser une URL comme :

```
    monsite/index?forceCache=1
```
Exemple de code (dans votre controler) pour activer et configurer le cache:

```
    if (IS_PROD || isset($_GET['forceCache'])) {
                $tpl->enableCache(true);
                $tpl->setCacheDir(CACHE_PATH);
                $tpl->setCacheTTL(600); // 10 min en prod
    }    
```

ATTENTION !!!
Le cache améliore les performances, mais introduit un risque évident :
 -> montrer du contenu obsolète si les données changent sans que le cache soit vidé ou régénéré.

Cas typique : un article modifié, mais la page montre l’ancienne version
En production, cela peut poser problème si :
    - le contenu dépend de la base de données ou de fichiers modifiables à chaud ;
    - l’utilisateur s’attend à voir des changements immédiatement ;
    - aucune invalidation du cache n’est prévue.

Ce que qu'il faut faire pour éviter ces pièges :

1. Limiter le cache aux pages réellement statiques
Ne mets en cache que ce qui :
    - ne dépend pas de données dynamiques volatiles ;
    - n'est pas modifié souvent (pages d’accueil, mentions légales, pages d’aide, etc.).

2. Réduire le TTL (durée de vie) du cache
Actuellement nous avons :

```
    $tpl->setCacheTTL(600); // 10 minutes    
```

On peut descendre à :
    - 60 secondes pour des pages semi-dynamiques,
    - ou utiliser une valeur dynamique basée sur le contenu.

3. Purger manuellement le cache sur modification
Quand on modifie une donnée, il faut appeler :

```
    \App\Core\CacheManager::clear();
```
On peut même l’automatiser par type de contenu si c'est un CMS :
```
    Post::update(...);
    CacheManager::clearViewCache('post-' . $postId);
```
4. Ne jamais cacher les pages privées / admin
Les pages avec sessions, données utilisateurs, ou contenu sensible :
    - jamais en cache fichier
    - générées à la volée

Ne pas activer le cache dans admin/



