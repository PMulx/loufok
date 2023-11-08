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

                $currentCadavre = $cadavreModel->getCurrentCadavre();

                $dateActuelle = date('Y-m-d');

                $this->display(
                    'joueur/listes.html.twig',
                    [
                        'dateActuelle' => $dateActuelle,
                        'id' => $id,
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

    public function cadavre($id, $idcadavre)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $this->display(
                    'joueur/cadavre.html.twig',
                    []
                );
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
                $texteContribution = $_POST['texteContribution'];
                $ordreSoumission = $_POST['ordreSoumission'];
                $dateSoumission = date('Y-m-d');

                HTTP::redirect("/joueur/{$id}");
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
                    // Le dernier cadavre terminé a été trouvé, vous pouvez l'afficher ou effectuer d'autres actions
                    var_dump($lastCadavre);
                    $this->display('joueur/lastcadavre.html.twig');
                } else {
                    // Aucun cadavre terminé n'a été trouvé, vous pouvez gérer cette situation selon vos besoins
                    $this->display('joueur/lastcadavre.html.twig');
                }
            }
        } else {
            HTTP::redirect('/');
        }
    }
}
