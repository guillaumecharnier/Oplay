## Installation de Symfony

la doc : https://symfony.com/doc/current/setup.html#creating-symfony-applications

### Sans container

```bash
# installation du projet
composer create-project symfony/skeleton

# on déplace les fichiers du dossier skeleton à la racine
sudo mv skeleton/* ./
sudo mv skeleton/.* ./
rmdir skeleton

# installation des composants de base
composer require twig symfony/asset symfony/apache-pack
composer require --dev symfony/profiler-pack symfony/maker-bundle symfony/debug-bundle

# installation de Twig
composer require twig

# installation Doctrine
composer require orm

# Commandes Doctrine

```bash
# supprimer les fichiers de migrations
# supprimer la bdd
bin/console doctrine:database:drop --force
# recréer la bdd
bin/console doctrine:database:create
# recréer un fichier de migration
bin/console make:migration
# appliquer les migrations
bin/console doctrine:migration:migrate
# appliquer une fixture
bin/console doctrine:fixtures:load
# crée une jwt key pair (à chaque deploy)
bin/console lexik:jwt:generate-keypair
```

# Commandes creation de tables

- créer l'entité `bin/console make:entity`
  - ajouter les annotations
- creer un controller 
  -`bin/console make:controller --no-template`
  -`bin/console make:controller`
  -`bin/console make:form`

- créer une migration avec : `bin/console make:migration`
- relire la migration
- appliquer la migration en BDD : `bin/console doctrine:migrations:migrate`


# E09 - fixtures et form

## Fixtures

La création de fausses données

- ne fait pas partie de l'application ( on n'en veux pas en prod donc pas dans un controller )
- est utile en dev ( pour avoir des données rapidement dans l'application )

On peut utiliser le composant fixtures.

Installation : `composer require --dev doctrine/doctrine-fixtures-bundle`

Ce dernier nous permet d'exécute une commande pour générer des fausses données.

On a donc déplacer le code de SandboxController::populateDatabase dans le fichier AppFixtures ( créé lors de l'installation )

Pour exécuter le code on lance : `bin/console doctrine:fixtures:load`


##  Sécurité Installation avec Symfony

cf [la doc](https://symfony.com/doc/current/security.html)

```bash
# installation du composant
composer require symfony/security-bundle
# création de l'entité User qui sera utilisée par le composant de sécurité
# cette entité est modifiable par la suite avec le make:entity
php bin/console make:user
# creer la migration et l'appliquer
bin/console make:migration
bin/console doctrine:migration:migrate

# dans adminer créer un utilisateur
# pour avoir un mdp hasher utiliser la commande suivante
bin/console security:hash-password
# pour le champ role il faut un tableau de chaine de caractere formaté en json
# par exemple
# /!\ Tous les roles doivent commencer par ROLE_
# ["ROLE_USER", "ROLE_ADMIN"]


# Création du formulaire de login
bin/console make:security:form-login
```


Pour créer un controller sans créer de template, on peut ajouter l'option `--no-template` au maker de controller

```bash
# créer un controller sans template
bin/console make:controller --no-template
# installer le composant de sérialization
composer require symfony/serializer-pack
```



### Authentification (api)

Pour authentifier un utilisateur, on a installé un composant qui génère un token cf [la doc du composant](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/3.x/Resources/doc/index.rst#installation)

```bash
# installer le composant
composer require lexik/jwt-authentication-bundle

# générer une paire de clef
bin/console lexik:jwt:generate-keypair

# configurer le composant de sécurité
## cf https://github.com/lexik/LexikJWTAuthenticationBundle/blob/3.x/Resources/doc/index.rst#configure-application-security

# créer une route de login
## cf https://github.com/lexik/LexikJWTAuthenticationBundle/blob/3.x/Resources/doc/index.rst#configure-application-routing
```

Pour s'authentifier le client doit

- appeler la route /api/login_check et fournir ses identifiants
- le client va recevoir un token qu'il devra stocker et fournir lors des prochains appels à l'API

### Autorisation

Pour l'autorisation, on devra utiliser `$this->isGranted` dans les controleurs car l'attribut `isGranted` et la méthode `$this->denyAccessUnlessGranted` ne renvoient pas du json.




## Empecher l'accès à des pages

On peut le faire soit dans le fichier `package/security.yaml`
ou alors dans le code

- dans un controlleruser
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'help' => "Laisser vide pour garder l'ancien",
                'mapped' => false
            ])
  - dans l'action `$this->denyAccessUnlessGranted('ROLE_ADMIN');`
  - ou avec un attribut `#[IsGranted('ROLE_ADMIN')]`
- dans un template avec `is_granted('ROLE_ADMIN')`

### Avec des Voters personnalisés

cf [la doc](https://symfony.com/doc/current/security/voters.html#checking-for-roles-inside-a-voter)

Lorsque les règles d'accès sont plus complexes ( en fonction d'un objet ou d'une date ) alors on peut écrire cette logique dans un Voter.

Un Voter est une classe qui implémente la classe `Component\Security\Core\Authorization\Voter\Voter` de Symfony et doit donc avoir deux méthodes :

- méthode `supports` pour dire si elle veux voter ou non
- méthode `voteOnAttribute` pour donner son vote

Cette logique sera appliquée en utilisant les fonctions suivantes

- `denyAccessUnlessGranted`
- `isGranted`
- et `is_granted` dans `twig`

