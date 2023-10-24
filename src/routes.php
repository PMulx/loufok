<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [

  ['GET', '/', 'connexion@index'],
  ['POST', '/connexion', 'connexion@index'],
  ['GET', '/inscription', 'connexion@inscription'],
  ['POST', '/inscription', 'connexion@createUser'],
  ['GET', '/disconnect', 'connexion@disconnect', 'disconnect'],

  ['GET', '/joueur/{id}', 'joueur@index'],
  ['GET', '/joueur/delete/{idEntreprise}/{idjoueur}', 'joueur@delete'],
  ['GET', '/joueur/add/{idEntreprise}/{idjoueur}', 'joueur@add'],
  ['GET', '/joueur/{id:\d+}/profil', 'joueur@profil'],

  ['POST', '/administrateur/{id:\d+}/add', 'administrateur@add'],
  ['GET', '/administrateur/{id}', 'administrateur@index'],

];
