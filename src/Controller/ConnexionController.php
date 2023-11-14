<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateurModel;

class ConnexionController extends Controller
{
    public function index()
    {
        $cadavreModel = new CadavreModel();
        $errorMessage = null;
        $_SESSION['role'] = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['mdp'];
            $cadavre = $cadavreModel::getInstance()->getCurrentCadavreId();
            $user = JoueurAdministrateurModel::getInstance()->checkLogin($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];

                if ($user['type'] === 'joueur') {
                    $_SESSION['role'] = 'joueur';
                    $user_id_string = strval($_SESSION['user_id']);

                    $randomContribution = $cadavreModel::getInstance()->getRandomContribution();

                    $maxOrdre = $cadavreModel::getInstance()->getCurrentSubmissionOrder();

                    if ($randomContribution === false) {
                        if ($maxOrdre >= 1) {
                            $numContributionAleatoire = mt_rand(1, $maxOrdre);

                            // Appeler une méthode pour attribuer aléatoirement une contribution
                            $cadavreModel->assignRandomContribution($cadavre, $_SESSION['user_id'], $numContributionAleatoire);
                        }

                        HTTP::redirect("/joueur/{$user['id']}");
                    } else {
                        // La contribution aléatoire existe déjà
                        HTTP::redirect("/joueur/{$user['id']}");
                    }
                } elseif ($user['type'] === 'administrateur') {
                    $_SESSION['role'] = 'administrateur';
                    HTTP::redirect("/administrateur/{$user['id']}");
                }
            } else {
                $errorMessage = 'Adresse e-mail ou mot de passe incorrect.';
            }
        }
        $this->display(
            'connexion/index.html.twig',
            ['errorMessage' => $errorMessage ?? null]
        );
    }

    public function logout()
    {
        $user = JoueurAdministrateurModel::getInstance()->logout();

        HTTP::redirect('/');
    }
}
