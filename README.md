# NWS Challenge

Application web développée dans le cadre de la formation à la Normandie Web School : une plateforme de mini-jeux avec comptes, niveaux et classement.

## Technologies

- Symfony 8, PHP 8.4
- PostgreSQL 16
- Twig, Doctrine ORM
- Docker, Docker Compose, Apache

## Lancer le projet

Il faut Docker Desktop (ou Docker Engine avec Compose).

```bash
docker compose up -d --build
```

- Site : http://localhost:8080
- Mailpit (emails de test) : http://localhost:8025

Au premier démarrage, les dépendances Composer s'installent automatiquement dans le conteneur PHP.

Pour arrêter le projet :

```bash
docker compose down
```

Pour supprimer aussi la base de données :

```bash
docker compose down -v
```

## Fonctionnalités

- Inscription, connexion, vérification de l'email, réinitialisation du mot de passe
- Niveaux, expérience (XP) et tokens
- Mini-jeu de puzzle (taquin) avec enregistrement du score
- Classement des joueurs
- Page musique (pistes récupérées via une API externe)
- Conditions générales d'utilisation
- Espace d'administration : gestion des utilisateurs (bannissement et déblocage), des jeux et des scores

## Auteur

Ayman EL HARI
