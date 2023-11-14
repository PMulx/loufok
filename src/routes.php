<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [
  ['GET', '/', 'connexion@index'],
  ['GET', '/connexion', 'connexion@index'],
  ['POST', '/connexion', 'connexion@index'],
  ['GET', '/inscription', 'connexion@inscription'],

  ['POST', '/inscription', 'connexion@createUser'],
  ['GET', '/disconnect', 'connexion@logout', 'logout'],

  ['GET', '/joueur/{id}', 'joueur@index'],
  ['POST', '/joueur/insert/{id}', 'joueur@insertaleatoire'],
  ['GET', '/joueur/cadavre/{id}/{idcadavre}', 'joueur@cadavre'],
  ['GET', '/joueur/lastcadavre/{id}', 'joueur@lastcadavre'],
  ['POST', '/joueur/contribution/{id}/{idcadavre}', 'joueur@insertcontribution'],

  ['POST', '/administrateur/{id}/add', 'administrateur@add'],
  ['GET', '/administrateur/{id}', 'administrateur@index'],
  ['GET', '/administrateur/{id}/currentCadavre', 'administrateur@currentCadavre'],
];
