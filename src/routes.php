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
  ['GET', '/disconnect', 'connexion@logout', 'logout'],

  ['GET', '/joueur/{id}', 'joueur@index'],
  ['POST', '/joueur/insert/{id}', 'joueur@insertaleatoire'],
  ['GET', '/joueur/lastcadavre/{id}', 'joueur@lastcadavre'],
  ['POST', '/joueur/contribution/{id}/{idcadavre}', 'joueur@insertcontribution'],

  ['POST', '/administrateur/{id}/add', 'administrateur@add'],
  ['GET', '/administrateur/{id}', 'administrateur@index'],
  ['GET', '/administrateur/joueur/{id}', 'administrateur@joueur'],
  ['POST', '/administrateur/joueur/{id}/played/{nomjoueur}', 'administrateur@insertPlay'],
  ['GET', '/administrateur/{id}/currentCadavre', 'administrateur@currentCadavre'],

  ['POST', '/api/cadavre/remove_like', 'api@removeLike'],
  ['POST', '/api/cadavre/add_like', 'api@addLike'],
  ['GET', '/api/cadavres', 'api@getAllCadavres'],
  ['GET', '/api/cadavre/{id}', 'api@getCadavre'],
];
