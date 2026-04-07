# Serenity - Système de Gestion de la serenité financiere

Serenity est une application web moderne basée sur Laravel, conçue pour simplifier la gestion des membres, des cotisations, des engagements et des flux financiers au sein d'une organisation ou d'une association.

## 🚀 Fonctionnalités Clés

### 👥 Gestion des Membres
- Inscription publique et gestion administrative des membres.
- Tableaux de bord personnalisés pour les membres et les administrateurs.
- Segmentation des membres pour une meilleure organisation.

### 💰 Gestion des Cagnottes, Tontines et Nano-crédits
- Création et suivi des campagnes de Cagnottes.
- Création et suivi des campagnes de Tontines.
- Gestion complète des nano-crédits par palliers.

### 💳 Paiements Multi-Moyens
- Intégration native avec **PayDunya**, **PayPal** et **Stripe**.
- Gestion des remboursements et suivi des transactions.

### 🏦 Gestion de Caisse
- Suivi précis des mouvements de caisse (Entrées, Sorties, Transferts).
- Journal de caisse et balance en temps réel.

### 📊 Rapports et Audit
- Rapports détaillés par membre, par cotisation et par caisse.
- Journal d'audit complet pour la traçabilité des actions.
- Traitement automatique de fin de mois.

### ✉️ Communication
- Campagnes d'emails groupés.
- Éditeur de modèles d'emails personnalisables.
- Logs complets des emails envoyés.

### 🛠 Administration et Sécurité
- Assistant d'installation pas à pas.
- Système de rôles et permissions granulaire.
- Gestion des sauvegardes (Backups) et restauration.

## 🛠 Stack Technique

- **Framework:** Laravel 12.x
- **Langage:** PHP 8.2+
- **Paiements:** PayDunya, PayPal, Stripe
- **PDF:** Laravel DOMPDF & Snappy
- **Déploiement:** Configuré pour cPanel (Git Version Control)

## 📦 Installation

### Prérequis
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Extension PHP (BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)

### Étapes d'installation locale

1. **Cloner le repository :**
   ```bash
   git clone ssh://bemo1278@bemo1278.odns.fr/home/bemo1278/repositories/ecotisations
   cd ecotisations
   ```

2. **Installer les dépendances :**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configuration de l'environnement :**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Installation via l'assistant :**
   Lancer l'application et accéder à `/install` via votre navigateur pour configurer la base de données et finaliser l'installation.

## 🌐 Déploiement (cPanel)

L'application est configurée pour un déploiement automatique sur cPanel via le fichier `.cpanel.yml`.

1. Poussez vos modifications sur le repository distant.
2. Dans cPanel, utilisez l'outil **Git™ Version Control**.
3. Cliquez sur **Pull or Deploy** pour mettre à jour le répertoire `/home/bemo1278/public_html/ecotisations.aladints.com`.

## 📜 Licence

Ce projet est sous licence MIT.
