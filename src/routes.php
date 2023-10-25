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
  ['POST', '/joueur/insert/{id}', 'joueur@insertaleatoire'],
  ['GET', '/joueur/cadavre/{id}/{idcadavre}', 'joueur@cadavre'],
  ['POST', '/joueur/contribution/{id}/{idcadavre}', 'joueur@insertcontribution'],

  ['POST', '/administrateur/{id:\d+}/add', 'administrateur@add'],
  ['GET', '/administrateur/{id}', 'administrateur@index'],

];
