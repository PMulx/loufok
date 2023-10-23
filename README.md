# Projet "loufok" - Jeu du Cadavre Exquis

Par Murail Anthonin.

## Description

"loufok" est une application web développée pour l'association "Un mot à dire", qui anime des ateliers d'écriture. Cette association souhaite exploiter la puissance d'Internet pour encourager l'écriture collective à travers une version moderne et ludique du jeu du cadavre exquis.

Le jeu du cadavre exquis est un jeu d'écriture collectif inventé par les surréalistes en 1925, où chaque participant écrit à tour de rôle une partie d'une phrase sans savoir ce que le précédent a écrit. L'objectif de "loufok" est de produire des histoires courtes et loufoques en respectant les règles et contraintes définies.

## Fonctionnalités clés

- Chaque contribution est un texte de 50 à 280 caractères, espaces compris.
- La première contribution est donnée par un membre de l'association, qui fixe également le nombre de contributions, la période de jeu et un titre pour identifier le futur cadavre.
- Le jeu est accessible après une phase d'authentification. Chaque joueur doit donc préalablement s'inscrire sur la plateforme de l'association en communiquant les informations suivantes : nom de plume (supposé unique), adresse mail, sexe, date de naissance et mot de passe.
- Lors d'un accès au jeu, le cadavre exquis est présenté au moyen d'une série de traits, matérialisant les différentes contributions. Une seule contribution, choisie aléatoirement au premier accès et identique pour les accès éventuels suivants, est affichée en clair. Le joueur est invité à soumettre son court récit grâce à une zone de texte.
- Un joueur ne peut soumettre qu'une seule contribution. Si le joueur accède à nouveau au jeu après avoir envoyé son texte, il verra uniquement la participation choisie aléatoirement et sa propre contribution correctement placées dans le cadavre exquis masqué.
- Une fois le nombre de participations ou la date de fin de jeu atteint, le message "Aucune fabrication en cours" sera affiché.
- Le joueur pourra à tout moment accéder (lire) le dernier cadavre auquel il a contribué.
