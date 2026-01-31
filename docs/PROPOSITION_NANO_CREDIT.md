# Proposition : Mise en place du Nano Crédit

## 1. État actuel

- **KYC** : en place (soumission par le membre, validation/rejet par l’admin, email + notification à la validation).
- **Contrôle d’accès** : la page « Nano Crédits » n’est accessible qu’aux membres dont le KYC est **validé**.
- **PayDunya** : déjà utilisé pour les paiements (cotisations, engagements) ; à réutiliser pour le décaissement et le recouvrement des nano crédits.

---

## 2. Modèle de données proposé

### 2.1 Produits Nano Crédit (`nano_credit_produits`)

Définis par l’admin, visibles par les membres pour faire une demande.

| Champ            | Type        | Description |
|------------------|-------------|-------------|
| id               | bigint      | Clé primaire |
| nom              | string      | Ex. "Nano Crédit Express", "Nano Crédit 3 mois" |
| description      | text        | Conditions affichées au membre |
| montant_min      | decimal     | Montant minimum du prêt (XOF) |
| montant_max      | decimal     | Montant maximum du prêt (XOF) |
| duree_mois       | integer     | Durée du remboursement en mois |
| taux_annuel      | decimal     | Taux d'intérêt annuel (%) |
| frais_dossier    | decimal     | Frais de dossier (XOF), optionnel |
| actif            | boolean     | Produit proposé ou non |
| ordre            | integer     | Ordre d’affichage |
| created_at, updated_at | timestamps | |

### 2.2 Demandes de Nano Crédit (`nano_credit_demandes`)

Une demande = un membre + un produit + un montant (et éventuellement une durée si on la rend variable plus tard).

| Champ            | Type        | Description |
|------------------|-------------|-------------|
| id               | bigint      | Clé primaire |
| membre_id        | FK          | Membre demandeur |
| produit_id       | FK          | Produit choisi |
| montant          | decimal     | Montant demandé (XOF) |
| duree_mois       | integer     | Durée (copie ou personnalisée) |
| statut           | enum        | en_attente, accorde, refuse |
| motif_refus      | text        | Si refus, motif communiqué au membre |
| accorde_le       | timestamp   | Date d’accord |
| accorde_par      | FK users    | Admin ayant accordé |
| refuse_le        | timestamp   | Date de refus |
| refuse_par       | FK users    | Admin ayant refusé |
| created_at, updated_at | timestamps | |

### 2.3 Crédits accordés / Contrats (`nano_credits`)

Un enregistrement par crédit effectivement accordé (après décaissement).

| Champ               | Type        | Description |
|---------------------|-------------|-------------|
| id                  | bigint      | Clé primaire |
| demande_id          | FK          | Lien vers la demande accordée |
| membre_id           | FK          | Membre |
| produit_id          | FK          | Produit utilisé |
| numero              | string      | Numéro unique du crédit (ex. NC-2026-0001) |
| montant_principal   | decimal     | Montant prêté (XOF) |
| taux_annuel         | decimal     | Taux appliqué |
| duree_mois          | integer     | Durée du remboursement |
| date_decaissement   | date        | Date du versement au membre |
| statut              | enum        | actif, rembourse, en_retard, defaut |
| paydunya_disbursement_id | string  | Référence PayDunya si versement via API |
| created_at, updated_at | timestamps | |

### 2.4 Échéances / Tableau d’amortissement (`nano_credit_echeances`)

Une ligne par échéance mensuelle.

| Champ            | Type        | Description |
|------------------|-------------|-------------|
| id               | bigint      | Clé primaire |
| nano_credit_id   | FK          | Crédit concerné |
| numero_echeance   | integer     | 1, 2, 3... |
| date_echeance     | date        | Date à laquelle la mensualité est due |
| montant_du        | decimal     | Part capital + intérêts de l’échéance |
| montant_paye      | decimal     | Montant déjà payé |
| statut            | enum        | a_venir, payee, partielle, en_retard |
| paydunya_invoice_id | string     | Référence facture PayDunya si paiement en ligne |
| paid_at           | timestamp   | Date du paiement effectif |
| created_at, updated_at | timestamps | |

### 2.5 Paiements Nano Crédit (`nano_credit_paiements`)

Historique des paiements liés aux échéances (pour tracer chaque versement).

| Champ            | Type        | Description |
|------------------|-------------|-------------|
| id               | bigint      | Clé primaire |
| nano_credit_id   | FK          | Crédit concerné |
| echeance_id      | FK nullable | Échéance concernée (si connu) |
| montant          | decimal     | Montant payé |
| mode_paiement    | string      | paydunya, especes, virement, etc. |
| reference        | string      | Référence externe (PayDunya, etc.) |
| date_paiement    | date        | Date du paiement |
| created_at, updated_at | timestamps | |

---

## 3. Parcours fonctionnel proposé

### 3.1 Côté Admin

1. **Paramétrage des produits**  
   - CRUD sur les produits nano crédit (nom, montant min/max, durée, taux, frais, actif, ordre).  
   - Liste compacte, style identique aux autres vues (segments, tags, etc.).

2. **Demandes en attente**  
   - Liste des demandes avec statut « en_attente ».  
   - Fiche demande : infos membre (déjà connues via KYC), produit choisi, montant, durée.  
   - Actions : **Accorder** ou **Refuser** (avec motif obligatoire en cas de refus).  
   - En cas d’accord : création du contrat (`nano_credits`) + génération du tableau d’amortissement (`nano_credit_echeances`).

3. **Décaissement (versement au membre)**  
   - Option A : **manuel** – l’admin marque « Décaissement effectué » (date, référence manuelle si besoin).  
   - Option B : **PayDunya** – appel API de décaissement (mobile money) si disponible ; enregistrement de l’ID de transaction.  
   - Notification + email au membre : « Votre nano crédit a été accordé et le montant a été versé sur votre mobile money » (ou « par virement » selon le cas).

4. **Suivi des crédits**  
   - Liste des crédits actifs / remboursés / en retard.  
   - Détail d’un crédit : tableau d’amortissement, historique des paiements, statut des échéances.

### 3.2 Côté Membre

1. **Catalogue des produits**  
   - Page « Nano Crédits » (déjà protégée par KYC validé) : liste des produits **actifs** avec montant min–max, durée, taux (et frais si présents).  
   - Texte court par produit (description / conditions).

2. **Demande de crédit**  
   - Clic sur un produit → formulaire : montant (entre min et max du produit), éventuellement durée si on la rend choisissable.  
   - Envoi de la demande → statut « en_attente ».  
   - Message de confirmation : « Votre demande a été enregistrée ; vous serez notifié par email et dans votre espace dès qu’une décision sera prise. »

3. **Mes crédits**  
   - Sous-menu ou onglet « Mes crédits » : liste des demandes (statut) et des crédits accordés.  
   - Pour chaque crédit actif/remboursé :  
     - Résumé (montant, durée, date de décaissement, statut).  
     - **Tableau d’amortissement** : échéances, montant dû, montant payé, date d’échéance, statut (à venir / payée / en retard).

4. **Remboursement**  
   - **Option simple (court terme)** : le membre paie via le lien PayDunya existant (comme pour une cotisation) ; l’admin ou un job enregistre le paiement et l’affecte à une échéance.  
   - **Option avancée** : génération automatique d’une facture PayDunya par échéance (ou regroupement de plusieurs échéances), avec rappel par email/notification avant échéance et lien de paiement.  
   - **Prélèvement automatique** : si PayDunya propose une API de prélèvement récurrent, l’intégrer plus tard pour « remboursement automatique ».

---

## 4. Intégration PayDunya

- **Paiements actuels** : PayDunya est déjà utilisé pour encaisser les cotisations et engagements.  
- **Décaissement (versement au membre)** :  
  - Vérifier si votre compte PayDunya (ou partenaire) permet le **paiement sortant** (mobile money vers le membre).  
  - Si oui : appel API après accord admin (montant, numéro du membre, référence du crédit), puis mise à jour de `nano_credits` (date_decaissement, paydunya_disbursement_id).  
  - Si non : garder un décaissement « manuel » (admin marque comme versé après virement / cash).  
- **Remboursement** :  
  - Réutiliser le flux « création facture PayDunya + callback » déjà en place : une facture = une échéance (ou un regroupement).  
  - Au retour du callback PayDunya : enregistrement dans `nano_credit_paiements`, mise à jour de l’échéance (montant_paye, statut) et du solde du crédit.

---

## 5. Plan de mise en place par étapes

### Phase 1 – Fondations (sans PayDunya décaissement)

1. Migrations : `nano_credit_produits`, `nano_credit_demandes`, `nano_credits`, `nano_credit_echeances`, `nano_credit_paiements`.  
2. Modèles Eloquent + relations.  
3. Admin : CRUD produits (liste compacte, style des autres vues).  
4. Membre : liste des produits actifs ; formulaire de demande (produit + montant) ; enregistrement de la demande.  
5. Admin : liste des demandes ; détail ; actions Accorder / Refuser (avec motif).  
6. À l’accord : création du contrat `nano_credits` + calcul et création des lignes `nano_credit_echeances` (amortissement).  
7. Décaissement « manuel » : champ « Décaissement effectué le » + notification + email au membre.

### Phase 2 – Suivi et remboursement manuel

1. Membre : page « Mes crédits » avec tableau d’amortissement en lecture seule.  
2. Admin : liste et détail des crédits ; suivi des échéances.  
3. Remboursement : soit saisie manuelle par l’admin (paiement reçu hors ligne), soit lien PayDunya « Payer cette échéance » pointant vers une facture créée pour l’échéance, avec callback qui enregistre le paiement et met à jour l’échéance et le statut du crédit.

### Phase 3 – Automatisation et décaissement PayDunya

1. Si API PayDunya le permet : décaissement automatique (versement mobile money) après accord admin.  
2. Rappels automatiques (cron) : X jours avant chaque échéance, email + notification au membre avec lien de paiement.  
3. (Optionnel) Prélèvement automatique si l’API PayDunya le gère.

---

## 6. Récapitulatif des écrans à prévoir

| Zone   | Écran                    | Description |
|--------|--------------------------|-------------|
| Admin  | Liste produits           | CRUD produits nano crédit, style liste compacte |
| Admin  | Liste demandes           | Filtre par statut (en_attente, accorde, refuse) |
| Admin  | Détail demande           | Infos membre + produit + montant ; boutons Accorder / Refuser |
| Admin  | Liste crédits            | Crédits actifs / remboursés / en retard |
| Admin  | Détail crédit            | Amortissement + historique des paiements |
| Membre | Nano Crédits (actuel)   | Liste des produits + bouton « Demander » par produit |
| Membre | Formulaire demande       | Choix du montant (et durée si variable) |
| Membre | Mes crédits              | Liste de ses crédits + tableau d’amortissement par crédit |
| Membre | Détail crédit            | Amortissement + bouton « Payer » par échéance (lien PayDunya) |

---

## 7. Points à valider avec vous

1. **Décaissement** : PayDunya est-il déjà configuré pour envoyer de l’argent au membre (mobile money) ? Si non, on démarre avec décaissement manuel.  
2. **Remboursement** : une échéance = une facture PayDunya (lien « Payer » par échéance) vous convient-il pour commencer ?  
3. **Taux / frais** : calcul des intérêts (mensualités constantes ? intérêts simples ?) et présence de frais de dossier à intégrer dès la phase 1.  
4. **Durée** : fixe par produit (ex. 3 mois) ou choix possible par le membre dans une fourchette (ex. 3 / 6 / 12 mois) ?

Dès que vous validez ces points et la structure ci-dessus, on peut détailler la phase 1 (migrations, modèles, CRUD produits, demande membre, accord/refus admin, création du crédit et des échéances) en tâches concrètes à coder dans votre projet Laravel.
