<?php

declare(strict_types=1); // Mode strict

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateurModel;

class ConnexionController extends Controller
{
    /**
     * Affiche la page de connexion et gère le processus de connexion.
     */
    public function index()
    {
        // Crée une instance de CadavreModel
        $cadavreModel = CadavreModel::getInstance();

        $notifications = isset($_SESSION['notifications']) ? $_SESSION['notifications'] : null;

        // Supprime les messages de confirmation de la session pour éviter qu'ils ne soient affichés à nouveau
        unset($_SESSION['notifications']);

        // Initialise le message d'erreur à null
        $errorMessage = null;

        // Initialise le rôle dans la session à null
        $_SESSION['role'] = null;

        // Vérifie la méthode de la requête HTTP (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupère les données du formulaire
            $email = $_POST['email'];
            $password = $_POST['mdp'];

            // Récupère l'ID du cadavre exquis actuel
            $cadavre = $cadavreModel->getCurrentCadavreId();

            // Vérifie les informations de connexion dans la base de données
            $user = JoueurAdministrateurModel::getInstance()->checkLogin($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];

                // Si l'utilisateur est un joueur
                if ($user['type'] === 'joueur') {
                    $_SESSION['role'] = 'joueur';
                    $id = $_SESSION['user_id'];

                    // Convertit l'ID de l'utilisateur en chaîne de caractères
                    $user_id_string = strval($_SESSION['user_id']);

                    // Récupère une contribution aléatoire existante ou attribue une nouvelle contribution
                    $randomContribution = $cadavreModel->getRandomContribution($id);
                    $maxOrdre = $cadavreModel->getCurrentSubmissionOrder();

                    if ($randomContribution === false) {
                        if ($maxOrdre >= 1) {
                            // Génère un numéro de contribution aléatoire
                            $numContributionAleatoire = mt_rand(1, (int) $maxOrdre);

                            // Appelle une méthode pour attribuer aléatoirement une contribution
                            $cadavreModel->assignRandomContribution($cadavre, $_SESSION['user_id'], $numContributionAleatoire);
                        }

                        // Redirige vers la page du joueur
                        HTTP::redirect("/joueur/{$user['id']}");
                    } else {
                        // La contribution aléatoire existe déjà
                        HTTP::redirect("/joueur/{$user['id']}");
                    }
                }
                // Si l'utilisateur est un administrateur
                elseif ($user['type'] === 'administrateur') {
                    $_SESSION['role'] = 'administrateur';

                    // Redirige vers la page de l'administrateur
                    HTTP::redirect("/administrateur/{$user['id']}");
                }
            } else {
                // Affiche un message d'erreur en cas d'informations de connexion incorrectes
                $errorMessage = 'Adresse e-mail ou mot de passe incorrect.';
            }
        }
        // Affiche la page de connexion avec le message d'erreur éventuel
        $this->display(
            'connexion/index.html.twig',
            [
                'errorMessage' => $errorMessage ?? null,
                'notifications' => $notifications
            ]
        );
    }

    /**
     * Déconnecte l'utilisateur et le redirige vers la page d'accueil.
     */
    public function logout()
    {
        // Appelle la méthode de déconnexion de JoueurAdministrateurModel
        $user = JoueurAdministrateurModel::getInstance()->logout();

        // Redirige vers la page d'accueil
        HTTP::redirect('/');
    }
}
