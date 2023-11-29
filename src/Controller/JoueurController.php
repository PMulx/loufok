<?php

declare(strict_types=1);

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateurModel;

class JoueurController extends Controller
{
    /**
     * Affiche la page principale du joueur.
     *
     * @param int $id Identifiant de l'utilisateur
     */
    public function index($id)
    {
        // Récupère la variable de session 'neverplayed' s'il existe
        $neverplayed = isset($_SESSION['neverplayed']) ? $_SESSION['neverplayed'] : null;

        // Supprime les messages de confirmation de la session pour éviter qu'ils ne soient affichés à nouveau
        unset($_SESSION['neverplayed']);

        // Vérifie si le rôle est défini dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige les administrateurs vers leur page spécifique
            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                // Récupère l'ID de l'utilisateur depuis la session
                $id = $_SESSION['user_id'];
                $nom = $_SESSION['user_name'];

                // Crée une instance de CadavreModel
                $cadavreModel = CadavreModel::getInstance();

                // Récupère une contribution aléatoire existante ou attribue une nouvelle contribution
                $randomContribution = $cadavreModel->getRandomContribution($id);
                $maxOrdre = $cadavreModel->getCurrentSubmissionOrder();

                if ($randomContribution === false) {
                    $randomContribution = 0;
                } else {
                    $randomContribution = 1;
                }

                // Récupère les contributions actuelles du cadavre exquis
                $currentCadavreContributions = $cadavreModel->getCurrentCadavre($role, $id);

                // Récupère l'ID du cadavre exquis actuel
                $getIdCadavre = $cadavreModel->getCurrentCadavreId();

                // Initialise l'ID du cadavre exquis à 0 s'il n'existe pas
                $idcadavre = $getIdCadavre != false ? $getIdCadavre : 0;

                // Récupère la date actuelle
                $dateActuelle = date('Y-m-d');

                // Affiche la page des listes du joueur avec les données pertinentes
                $this->display(
                    'joueur/listes.html.twig',
                    [
                        'randomcontribution' => $randomContribution,
                        'nom' => $nom,
                        'dateActuelle' => $dateActuelle,
                        'id' => $id,
                        'idcadavre' => $idcadavre,
                        'currentCadavreContributions' => $currentCadavreContributions,
                        'neverplayed' => $neverplayed,
                    ]
                );
            }
        } else {
            // Redirige vers la page d'accueil si le rôle n'est pas défini
            HTTP::redirect('/');
        }
    }

    /**
     * Ajoute une contribution au cadavre exquis.
     *
     * @param int $id        Identifiant de l'utilisateur
     * @param int $idcadavre Identifiant du cadavre exquis
     */
    public function insertcontribution($id, $idcadavre)
    {
        // Vérifie si le rôle est défini dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige les administrateurs vers leur page spécifique
            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                // Récupère le texte de la contribution depuis le formulaire
                $text = $_POST['texteContribution'];
                $joueurId = $_SESSION['user_id'];
                $cadavreId = $_POST['cadavreId'];

                // Crée une instance de CadavreModel
                $cadavreModel = CadavreModel::getInstance();

                // Appelle la méthode qui peut retourner des erreurs
                $errorMessages = $cadavreModel->addJoueurContribution($cadavreId, $joueurId, $text);
                $dernierecontribution = $cadavreModel->getCurrentCadavre($role, $id);

                $id = $_SESSION['user_id'];

                if (empty($dernierecontribution['data'])) {
                    // Redirection spécifique lorsque 'data' est vide
                    HTTP::redirect("/joueur/lastcadavre/{$id}");
                } else {

                    // Affiche la page d'erreur en cas d'erreurs, sinon redirige vers la page du joueur
                    if (!empty($errorMessages)) {
                        $this->display(
                            'joueur/error.html.twig',
                            [
                                'errorMessages' => $errorMessages,
                                'id' => $id,
                            ]
                        );
                    } else {
                        HTTP::redirect("/joueur/{$id}");
                    }
                }
            }
        } else {
            // Redirige vers la page d'accueil si le rôle n'est pas défini
            HTTP::redirect('/');
        }
    }

    /**
     * Affiche la page du dernier cadavre exquis auquel le joueur a participé.
     *
     * @param int $id Identifiant de l'utilisateur
     */
    public function lastcadavre($id)
    {
        // Vérifie si le rôle est défini dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige les administrateurs vers leur page spécifique
            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                // Récupère l'ID de l'utilisateur depuis la session
                $id = $_SESSION['user_id'];

                // Récupère les informations complètes du dernier cadavre exquis auquel le joueur a participé
                $lastCadavre = JoueurAdministrateurModel::getInstance()->getCompleteCadavreInfo($id);

                // Affiche la page du dernier cadavre exquis avec les données pertinentes
                if ($lastCadavre) {
                    $this->display(
                        'joueur/lastcadavre.html.twig',
                        [
                            'lastcadavre' => $lastCadavre,
                            'id' => $id,
                        ]
                    );
                } else {
                    // Redirige vers la page du joueur avec un message si le joueur n'a pas encore participé à un cadavre
                    HTTP::redirect("/joueur/{$id}");
                    $_SESSION['neverplayed'] = "Vous n'avez pas encore participé à un cadavre exquis qui est maintenant clos.";
                }
            }
        } else {
            // Redirige vers la page d'accueil si le rôle n'est pas défini
            HTTP::redirect('/');
        }
    }
}
