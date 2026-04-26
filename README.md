# Projet Nexora

Bienvenue dans le répertoire du projet **Nexora**, une plateforme web sociale en cours de développement sous **Laravel**.

Ce document résume toutes les fondations et fonctionnalités qui ont été générées et mises en place sur le projet jusqu'à présent.

## 🛠️ Stack Technique
- **Framework Backend** : Laravel (PHP)
- **Base de données** : MySQL
- **Frontend** : Tailwind CSS, Blade, Vanilla JS
- **Authentification tierce** : Laravel Socialite

---

## 🏗️ Architecture de Base de Données

Un schéma de base de données relationnel complet a été implémenté via les **Migrations Laravel**, comprenant 16 tables avec toutes les clés étrangères, suppressions en cascade et clés primaires personnalisées configurées. 

L'ordre d'exécution a été sécurisé (par exemple, `niveaus` avant `profils`) pour garantir l'intégrité référentielle :
1. `utilisateurs` (Inclut Nom, Prénom, Email, Mot de passe optionnel, ID Provider OAuth)
2. `niveaus`
3. `profils` (Reliée à utilisateurs et niveaus)
4. `centreinterets`
5. `choisirinterets` (Table pivot)
6. `amities` & `blocages` (Relations entre profils)
7. `matchings` (Score d'affinité)
8. `conversations`, `messages`, `partciperconvs` & `rooms` (Système de messagerie)
9. `badges` & `possederbadges` (Gamification)
10. `notifications`
11. `compagnonvirtuels` (Bots IA)

## 🧩 Modèles (Eloquent ORM)

Pour chaque table, un Modèle Eloquent respectant les conventions Laravel (Singulier, Majuscule) a été généré :
`Utilisateur`, `Niveau`, `Profil`, `Centreinteret`, `Choisirinteret`, `Amitie`, `Blocage`, `Matching`, `Conversation`, `Partciperconv`, `Message`, `Room`, `Badge`, `Possederbadge`, `Notification`, `Compagnonvirtuel`.

Le modèle `Utilisateur` a été spécifiquement configuré avec sa table (`utilisateurs`), sa clé primaire (`idUtilisateur`), et ses champs assignables en masse (`$fillable`).

## 🔐 Système d'Authentification Avancé

Un système d'authentification double a été développé sur-mesure (`AuthController`) :

1. **Authentification Classique** :
   - Inscription complète avec *Nom, Prénom, Email et Mot de passe*.
   - Connexion via Email et Mot de passe.
   
2. **Authentification Sociale (OAuth)** :
   - Intégration de **Laravel Socialite**.
   - Plateformes supportées : **Google, GitHub, X (Twitter) et Apple**.
   - Extraction automatique du prénom et du nom à partir du profil du réseau social, avec gestion d'utilisateurs sans mot de passe requis.

3. **Interface Utilisateur (UI)** :
   - Fichier : `resources/views/auth/login.blade.php`.
   - Interface responsive stylisée avec **Tailwind CSS**.
   - Système d'onglets dynamiques (Javascript pur) pour basculer de manière fluide entre le formulaire de connexion et le formulaire d'inscription.
   - Boutons de réseaux sociaux colorés avec icônes FontAwesome intégrées.

## 🚀 Comment lancer le projet

1. Assurez-vous d'avoir complété votre fichier `.env` avec les identifiants de base de données.
2. Appliquez les migrations si ce n'est pas déjà fait :
   ```bash
   php artisan migrate:fresh
   ```
3. (Optionnel) Configurez les identifiants OAuth (`GOOGLE_CLIENT_ID`, etc.) dans votre `.env`.
4. Démarrez le serveur local :
   ```bash
   php artisan serve
   ```
5. Accédez à la page d'authentification sur `http://localhost:8000/login`.
