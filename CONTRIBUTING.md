# Guide de Collaboration - Projet Ecotisations

Ce document définit les règles de travail pour assurer une collaboration fluide et éviter les régressions (erreurs de migrations, conflits Git).

## 1. Gestion des Branches

- **Branche `main`** : Contient uniquement le code stable et testé. **Ne jamais travailler directement sur cette branche.**
- **Branches Fonctionnalités (`feat/`)** : Chaque nouvelle tâche ou module doit avoir sa propre branche.
    - Format : `feat/nom-du-module` (ex: `feat/gestion-versements`, `feat/kyc-v2`)
- **Branches Corrections (`fix/`)** : Pour les corrections de bugs.
    - Format : `fix/nom-du-bug`

## 2. Workflow de Développement (Git)

### Débuter une tâche
1. S'assurer d'être sur la branche principale : `git checkout main`
2. Récupérer le dernier code : `git pull origin main`
3. Mettre à jour sa base de données : `php artisan migrate`
4. Créer sa branche : `git checkout -b feat/votre-tache`

### Enregistrer son travail
1. Indexer les fichiers : `git add .`
2. Commiter avec un message clair : `git commit -m "feat: ajout de la validation OTP"`
3. Envoyer vers le serveur : `git push origin feat/votre-tache`

### Fusionner son travail
Une fois le module terminé et testé localement :
1. Sur GitHub/GitLab, créer une **Pull Request (PR)** vers `main`.
2. Demander une relecture au collaborateur.
3. Après validation, fusionner la PR.

## 3. Règles Spécifiques à Laravel

### Les Migrations (CRITIQUE)
- **Règle d'or** : Une migration déjà poussée sur `main` ne doit **JAMAIS** être modifiée.
- Si vous devez modifier une table existante (ex: ajouter une colonne `telephone`), créez une nouvelle migration :
  `php artisan make:migration add_telephone_to_membres_table`
- Utilisez toujours `Schema::disableForeignKeyConstraints()` et `Schema::enableForeignKeyConstraints()` dans vos méthodes `up()` et `down()` pour éviter les erreurs SQL 1824 et consorts lors des `migrate:fresh`.

### Les Dépendances
- Si vous installez un nouveau package (via `composer` ou `npm`), prévenez votre collaborateur pour qu'il puisse exécuter `composer install` ou `npm install`.

## 4. Communication
- Utilisez les issues GitHub ou un outil de gestion de tâches pour savoir qui travaille sur quoi.
- En cas de gros changement structurel (ex: renommage de modèle), discutez-en avant l'implémentation.
