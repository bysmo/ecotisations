# Proposition : Épargne automatique

## 1. Principe général

L’**épargne automatique** permet à un membre de **s’engager à verser un montant à une fréquence donnée** (ex. 5 000 XOF chaque 1er du mois). Le système :

- enregistre son **engagement** (montant + fréquence) ;
- **à chaque échéance**, crée une « échéance d’épargne » et envoie un **rappel** (email + notification) avec un **lien de paiement PayDunya** ;
- enregistre chaque **versement** et met à jour le **solde d’épargne** du membre.

L’« automatique » porte donc sur : **rappel systématique + lien de paiement à chaque échéance**, sans prélèvement forcé (sauf si une API de prélèvement récurrent est disponible plus tard).

---

## 2. Modèle de données proposé

### 2.1 Plans d’épargne (`epargne_plans`)

Définis par l’admin : types d’épargne proposés aux membres.

| Champ         | Type    | Description |
|---------------|---------|-------------|
| id            | bigint  | Clé primaire |
| nom           | string  | Ex. "Épargne Libre", "Épargne 10k" |
| description   | text    | Texte affiché au membre |
| montant_min   | decimal | Versement minimum par échéance (XOF) |
| montant_max   | decimal | Versement maximum par échéance (XOF), nullable = illimité |
| frequence     | enum    | hebdomadaire, mensuel, trimestriel |
| actif         | boolean | Proposé ou non |
| ordre         | integer | Ordre d’affichage |
| created_at, updated_at | timestamps | |

### 2.2 Souscriptions épargne (`epargne_souscriptions`)

Un membre souscrit à un plan avec un montant et une date de début.

| Champ            | Type    | Description |
|------------------|---------|-------------|
| id               | bigint  | Clé primaire |
| membre_id        | FK      | Membre |
| plan_id          | FK      | Plan choisi |
| montant          | decimal | Montant choisi par versement (XOF) |
| date_debut       | date    | À partir de quand les échéances commencent |
| jour_du_mois     | integer | Pour mensuel : jour du mois (1-28), null si autre fréquence |
| statut           | enum    | active, suspendue, cloturee |
| solde_courant    | decimal | Solde total épargné (mis à jour à chaque versement) |
| created_at, updated_at | timestamps | |

### 2.3 Caisse dédiée (optionnel)

- Soit une **caisse existante** « Épargne » dans votre table `caisses`.
- Soit un champ **`caisse_id`** sur `epargne_plans` pour associer chaque plan à une caisse.
- Chaque versement = un **mouvement** vers cette caisse (comme un paiement de cotisation), pour garder la comptabilité claire.

### 2.4 Échéances d’épargne (`epargne_echeances`)

Une ligne par « date à laquelle le membre doit verser X XOF ».

| Champ            | Type    | Description |
|------------------|---------|-------------|
| id               | bigint  | Clé primaire |
| souscription_id  | FK      | Souscription concernée |
| date_echeance    | date    | Date du versement attendu |
| montant          | decimal | Montant à verser |
| statut           | enum    | a_venir, payee, en_retard, annulee |
| paye_le          | timestamp | Date du paiement effectif |
| created_at, updated_at | timestamps | |

### 2.5 Versements épargne (`epargne_versements`)

Chaque paiement effectué par le membre (lien possible avec `paiements` ou table dédiée).

| Champ            | Type    | Description |
|------------------|---------|-------------|
| id               | bigint  | Clé primaire |
| souscription_id  | FK      | Souscription concernée |
| echeance_id      | FK      | Échéance payée (nullable si paiement hors échéance) |
| membre_id        | FK      | Membre |
| montant          | decimal | Montant versé |
| date_versement   | date    | Date du versement |
| mode_paiement    | string  | paydunya, especes, virement... |
| reference        | string  | Référence PayDunya ou autre |
| caisse_id        | FK      | Caisse créditée |
| created_at, updated_at | timestamps | |

- À chaque enregistrement d’un versement : mise à jour de `epargne_souscriptions.solde_courant` et de `epargne_echeances.statut` (payee) si une échéance est concernée.

---

## 3. Fonctionnement détaillé

### 3.1 Côté Admin

1. **Plans d’épargne**
   - CRUD : nom, description, montant min/max par versement, fréquence (hebdo / mensuel / trimestriel), caisse associée, actif, ordre.
   - Liste compacte (même style que segments, tags, etc.).

2. **Suivi**
   - Liste des souscriptions (membre, plan, montant, fréquence, solde, statut).
   - Liste des échéances à venir / en retard (optionnel).
   - Historique des versements par caisse ou par plan.

### 3.2 Côté Membre

1. **Souscription**
   - Page « Épargne » (nouveau lien dans le menu membre) : liste des **plans actifs**.
   - Clic sur un plan → formulaire : montant (entre min et max du plan), date de début, pour **mensuel** : choix du jour du mois (ex. 1, 15).
   - Envoi → création de la souscription + génération des **premières échéances** (ex. 12 échéances mensuelles pour 1 an, ou génération au fil de l’eau).

2. **Mes épargnes**
   - Liste des souscriptions (actives / clôturées) : plan, montant par versement, fréquence, **solde courant**, prochaine échéance.
   - Détail d’une souscription : tableau des échéances (date, montant, statut) + historique des versements.

3. **Paiement d’une échéance**
   - Bouton « Payer » sur l’échéance à venir ou en retard → même flux que cotisation : création facture PayDunya (montant = échéance, référence = type épargne + id échéance) → callback PayDunya enregistre le versement, met à jour l’échéance et le solde.

### 3.3 Automatisation (tâche planifiée)

- **Cron quotidien** (ex. `app:epargne-echeances`) :
  1. Pour chaque souscription **active**, selon la **fréquence** et le **jour** :
     - Calculer les prochaines échéances (ex. pour mensuel, 1er du mois : générer l’échéance du 1er du mois prochain si pas déjà créée).
  2. Pour les échéances dont la **date d’échéance = aujourd’hui** (ou dépassée) et statut **a_venir** :
     - Passer en **en_retard** si date &lt; aujourd’hui.
     - Envoyer un **rappel** (email + notification) avec **lien de paiement PayDunya** (création d’une facture pour ce montant, custom data = type épargne, souscription_id, echeance_id).

- Option : envoyer le rappel **J jours avant** (ex. 2 jours avant l’échéance) en plus du jour J, comme pour les cotisations.

### 3.4 Callback PayDunya (épargne)

- Dans le callback existant (ou une branche dédiée), selon `custom_data` :
  - Si `type = 'epargne'` : créer `epargne_versements`, lier à `souscription_id` et `echeance_id`, créditer la caisse, mettre à jour `solde_courant` et `echeance.statut = payee`.
  - Génération des **prochaines échéances** si on les crée au fil de l’eau (ex. après paiement de l’échéance N, créer l’échéance N+1).

---

## 4. Récapitulatif des écrans

| Zone  | Écran              | Description |
|-------|--------------------|-------------|
| Admin | Liste plans        | CRUD plans d’épargne (style liste compacte) |
| Admin | Liste souscriptions| Filtres par plan, membre, statut |
| Admin | Liste versements   | Historique des versements (optionnel) |
| Membre| Épargne            | Liste des plans + bouton « Souscrire » |
| Membre| Souscrire          | Formulaire montant + date début + jour (si mensuel) |
| Membre| Mes épargnes       | Liste souscriptions + solde + prochaine échéance |
| Membre| Détail souscription| Échéances + bouton « Payer » + historique |

---

## 5. Intégration avec l’existant

- **Caisses** : chaque versement = entrée en caisse (comme un paiement de cotisation), soit sur une caisse « Épargne » dédiée, soit une caisse par plan (selon votre choix).
- **PayDunya** : réutilisation du même flux (création facture + callback) ; dans le callback, selon le type (cotisation / engagement / **épargne**), enregistrer dans la bonne table et mettre à jour la bonne entité (échéance + solde).
- **Notifications / Emails** : même mécanisme que pour les rappels de cotisation (EmailService + config SMTP admin, notifications en base pour le dashboard membre).
- **Menu membre** : ajout d’un lien « Épargne » (ou « Mon épargne ») pointant vers la liste des plans puis « Mes épargnes ».

---

## 6. Ordre de mise en œuvre suggéré

1. **Migrations** : `epargne_plans`, `epargne_souscriptions`, `epargne_echeances`, `epargne_versements`.
2. **Modèles** + relations (Membre, Caisse, etc.).
3. **Admin** : CRUD plans d’épargne (liste compacte).
4. **Membre** : page liste des plans + formulaire de souscription + enregistrement souscription + génération des premières échéances (ex. 12 mois pour mensuel).
5. **Membre** : page « Mes épargnes » (liste souscriptions + détail avec échéances + bouton « Payer »).
6. **Paiement** : création facture PayDunya pour une échéance (montant, custom_data type=epargne, souscription_id, echeance_id) + dans le callback, traitement épargne (versement, solde, statut échéance).
7. **Cron** : génération des échéances manquantes (si besoin) + rappels (email + notification + lien PayDunya) pour les échéances du jour ou en retard.

Si vous validez cette approche (engagement membre + rappel + paiement PayDunya par échéance), on peut détailler la phase 1 (tables + CRUD plans + souscription membre + génération échéances) en tâches concrètes à coder dans votre projet Laravel. Si vous voulez un autre fonctionnement (ex. prélèvement automatique dès que l’API le permet), on peut l’indiquer comme évolution dans le même document.
