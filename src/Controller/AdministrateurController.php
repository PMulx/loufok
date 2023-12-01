<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateurModel;

session_start();

class AdministrateurController extends Controller
{
    /**
     * Affiche la page principale d'administration.
     *
     * @param int $id Identifiant de l'utilisateur
     */
    public function index($id)
    {
        // Récupère les messages de confirmation de la session, s'il y en a
        $confirmMessages = isset($_SESSION['confirmMessages']) ? $_SESSION['confirmMessages'] : null;

        // Supprime les messages de confirmation de la session pour éviter qu'ils ne soient affichés à nouveau
        unset($_SESSION['confirmMessages']);

        // Vérifie si l'utilisateur a un rôle valide dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige le rôle "joueur" vers sa page spécifique
            if ($role === 'joueur') {
                HTTP::redirect("/joueur/{$id}");
            } else {
                // Récupère la date actuelle
                $dateActuelle = date('Y-m-d');

                // Récupère l'ID de l'utilisateur depuis la session
                $id = $_SESSION['user_id'];
                $nom = $_SESSION['user_name'];

                // Crée une instance de CadavreModel
                $cadavreModel = CadavreModel::getInstance();

                // Récupère toutes les périodes et titres pour l'affichage
                $periodesModel = $cadavreModel->getAllPeriods();
                $titles = $cadavreModel->getAllTitles();
                $cadavres = $cadavreModel->getInfoCadavres();

                // Transforme les périodes dans un format plus facile à gérer
                $periodes = [];
                foreach ($periodesModel as $periodeModel) {
                    $dateParts = explode(' | ', $periodeModel['periode']);
                    $periodes[] = [
                        'start' => $dateParts[0],
                        'end' => $dateParts[1],
                    ];
                }

                // Affiche la page d'administration avec les données pertinentes
                $this->display(
                    'administrateur/admin.html.twig',
                    [
                        'cadavres' => $cadavres,
                        'nom' => $nom,
                        'titles' => json_encode($titles),
                        'periodes' => $periodes,
                        'dateActuelle' => $dateActuelle,
                        'popup' => isset($_SESSION['popup']) ? $_SESSION['popup'] : null,
                        'id' => $id,
                        'confirmMessages' => $confirmMessages,
                    ]
                );
            }
        } else {
            // Redirige vers la page d'accueil si l'utilisateur n'a pas de rôle valide
            HTTP::redirect('/');
        }
    }

    /**
     * Affiche la page du cadavre exquis actuel.
     *
     * @param int $id Identifiant de l'utilisateur
     */
    public function currentcadavre($id)
    {
        // Vérifie si l'utilisateur a un rôle valide dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige le rôle "joueur" vers sa page spécifique
            if ($role === 'joueur') {
                HTTP::redirect("/joueur/{$id}");
            } else {
                // Récupère l'ID de l'utilisateur depuis la session
                $id = $_SESSION['user_id'];
                $nom = $_SESSION['user_name'];

                // Crée une instance de CadavreModel
                $cadavreModel = CadavreModel::getInstance();

                // Récupère les cadavres exquis actuels en fonction du rôle et de l'ID de l'utilisateur
                $currentCadavres = $cadavreModel->getCurrentCadavre($role, $id);

                // Affiche la page du cadavre exquis actuel avec les données pertinentes
                $this->display(
                    'administrateur/currentcadavre.html.twig',
                    [
                        'currentCadavres' => $currentCadavres,
                        'id' => $id,
                    ]
                );
            }
        } else {
            // Redirige vers la page d'accueil si l'utilisateur n'a pas de rôle valide
            HTTP::redirect('/');
        }
    }


    public function joueur($id)
    {
        // Vérifie si l'utilisateur a un rôle valide dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige le rôle "joueur" vers sa page spécifique
            if ($role === 'joueur') {
                HTTP::redirect("/joueur/{$id}");
            } else {
                // Récupère l'ID de l'utilisateur depuis la session
                $id = $_SESSION['user_id'];

                // Crée une instance de CadavreModel
                $cadavreModel = JoueurAdministrateurModel::getInstance();

                // Récupère les cadavres exquis actuels en fonction du rôle et de l'ID de l'utilisateur
                $getAlljoueurinfos = $cadavreModel->getJoueurInfo();

                // Affiche la page du cadavre exquis actuel avec les données pertinentes
                $this->display(
                    'administrateur/joueurs.html.twig',
                    [
                        'joueurinfos' => $getAlljoueurinfos,
                        'id' => $id,
                    ]
                );
            }
        } else {
            // Redirige vers la page d'accueil si l'utilisateur n'a pas de rôle valide
            HTTP::redirect('/');
        }
    }

    public function insertPlay($id, $nomjoueur)
    {
        // Vérifie si l'utilisateur a un rôle valide dans la session
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Redirige le rôle "joueur" vers sa page spécifique
            if ($role === 'joueur') {
                $id = $_SESSION['user_id'];
                HTTP::redirect("/joueur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $nomjoueur = $_POST['playerName'];
                $canplay = $_POST['toggleValue'];

                $administrateurJoueurModel = JoueurAdministrateurModel::getInstance();

                // Insère la contribution du cadavre exquis dans la base de données
                $returnMessages = $administrateurJoueurModel->updateJoueurCanplay($nomjoueur, $canplay);

                // Affiche la page du cadavre exquis actuel avec les données pertinentes
                HTTP::redirect("/administrateur/joueur/{$id}");
            }
        } else {
            // Redirige vers la page d'accueil si l'utilisateur n'a pas de rôle valide
            HTTP::redirect('/');
        }
    }

    /**
     * Ajoute un nouveau cadavre exquis.
     *
     * @param int $id Identifiant de l'utilisateur
     */
    public function add($id)
    {
        // Vérifie la méthode de la requête HTTP (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère les données du formulaire
            $datas = [
                'title' => $_POST['titre'],
                'dateStart' => $_POST['dateDebut'],
                'dateEnd' => $_POST['dateFin'],
                'adminId' => $id,
                'text' => $_POST['texteContribution'],
                'nbMaxContributions' => $_POST['nbContributions'],
            ];

            // Récupère l'ID de l'utilisateur depuis la session
            $id = $_SESSION['user_id'];

            // Crée une instance de CadavreModel
            $cadavreModel = CadavreModel::getInstance();

            // Insère la contribution du cadavre exquis dans la base de données
            $returnMessages = $cadavreModel->insertCadavreContribution($datas);

            // Récupère les messages d'erreur et de confirmation
            $errorMessages = $returnMessages['errors'];
            $confirmMessages = $returnMessages['success'];

            // Affiche la page d'erreur en cas d'erreurs, sinon redirige vers la page d'administration
            if (!empty($errorMessages)) {
                $this->display(
                    'administrateur/error.html.twig',
                    [
                        'errorMessages' => $errorMessages,
                        'id' => $id,
                    ]
                );
            } else {
                $_SESSION['confirmMessages'] = $confirmMessages;
                $_SESSION['notifications'] = "Un nouveau cadavre exquis vient d'être crée.";

                HTTP::redirect("/administrateur/{$id}");
            }
        } else {
            // Affiche un message d'erreur si la méthode de la requête n'est pas POST
            var_dump('ERROR');
        }
    }
}
