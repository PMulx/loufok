<?php

declare(strict_types=1);

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateurModel;

class JoueurController extends Controller
{
    public function index($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $cadavreModel = new CadavreModel();

                $currentCadavreContributions = $cadavreModel->getCurrentCadavre($role, $id);

                $getIdCadavre = $cadavreModel->getCurrentCadavreId();

                if ($getIdCadavre != false) {
                    $idcadavre = $getIdCadavre["id_cadavre"];
                } else {
                    $idcadavre = 0;
                }
                $dateActuelle = date('Y-m-d');

                $this->display(
                    'joueur/listes.html.twig',
                    [
                        'dateActuelle' => $dateActuelle,
                        'id' => $id,
                        'idcadavre' => $idcadavre,
                        'currentCadavreContributions' => $currentCadavreContributions,
                    ]
                );
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function insertaleatoire($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];
                $idCadavre = $_POST['cadavreEnCoursID'];
                $nbAleatoire = $_POST['nbAleatoire'];

                HTTP::redirect("/joueur/cadavre/{$id}/{$idCadavre}");
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function insertcontribution($id, $idcadavre)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $text = $_POST['texteContribution'];
                $joueurId = $_SESSION['user_id'];
                $cadavreId = $_POST['cadavreId'];

                $cadavreModel = new CadavreModel();

                // Appel à la méthode qui peut retourner des erreurs
                $errorMessages = $cadavreModel->addJoueurContribution($cadavreId, $joueurId, $text);

                if (!empty($errorMessages)) {

                    $this->display(
                        'joueur/error.html.twig',
                        [
                            'errorMessages' => $errorMessages,
                        ]
                    );
                } else {
                    HTTP::redirect("/joueur/{$id}");
                }
            }
        } else {
            HTTP::redirect('/');
        }
    }


    public function lastcadavre($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $lastCadavre = JoueurAdministrateurModel::getInstance()->getCompleteCadavreInfo($id);
                if ($lastCadavre) {
                    $this->display(
                        'joueur/lastcadavre.html.twig',
                        [
                            'lastcadavre' => $lastCadavre,
                            'id' => $id,
                        ]
                    );
                } else {
                    HTTP::redirect("/joueur/{$id}");
                }
            }
        } else {
            HTTP::redirect('/');
        }
    }
}
